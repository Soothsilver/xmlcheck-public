<?php

namespace asm\core;
use
	asm\plugin\PluginResponse,
	asm\utils\ShellUtils,
	asm\utils\StringUtils,
	asm\utils\Logger,
	Exception;

/**
 * Core functions (communications between components, error logging) @module.
 */
class Core
{
    /**
     * @var \asm\utils\Logger logger instance
     */
	protected static $logger;
    /**
     * @var string name of UI request being handled
     */
	protected static $request = null;

	/**
	 * [Initializes mailer and] sends e-mail.
	 * @param string $to recipient e-mail address
	 * @param string $subject subject
	 * @param string $body text
     * @return bool was the email successfully sent?
	 */
	public static function sendEmail ($to, $subject, $body)
	{
        $config = Config::get('mail');
        $host = isset($config['host']) ? $config['host'] : 'localhost';
        $port = isset($config['port']) ? $config['port'] : 25;
        $security = isset($config['security']) ? $config['security'] : null;
        $from_name = isset($config['from_name']) ? $config['from_name'] : "XMLCHECK";
        $from_addr = isset($config['from_address']) ? $config['from_address'] : "XMLCHECK@XMLCHECK.CZ";

        $transport = \Swift_SmtpTransport::newInstance($host, $port, $security);
        if ($security)
        {
           $transport->setUsername($config["user"])->setPassword($config["password"]);
        }
        $mailer = \Swift_Mailer::newInstance($transport);
        /**
         * @var $message \Swift_Mime_Message
         */
        $message = \Swift_Message::newInstance($subject)
            ->setFrom($from_addr, $from_name)
            ->setTo(array($to))
            ->setBody($body);

        return ($mailer->send($message) === 1);
	}

    /**
     * Launches plugin and updates database with results.
     * @param string $pluginType one of 'php', 'java', or 'exe'
     * @param string $pluginFile plugin file path
     * @param string $inputFile input file path
     * @param boolean $isTest is this a plugin test or a submission? true if plugin test
     * @param int $rowId ID of row to be updated
     * @param array $arguments plugin arguments
     * @return bool true if no error occurred
     */
	public static function launchPlugin ($pluginType, $pluginFile, $inputFile,
			$isTest, $rowId, $arguments = array())
	{
        try
        {
            $response = null;
            if (!is_file($pluginFile) || !is_file($inputFile))
            {
                $error = "plugin file and/or input file don't exist";
            }
            else
            {
                array_unshift($arguments, $inputFile);

                $cwd = getcwd();
                chdir(dirname($pluginFile));
                switch ($pluginType)
                {
                    case 'php':
                        $launcher = new PhpLauncher();
                        ob_start();
                        $error = $launcher->launch($pluginFile, $arguments, $response);
                        ob_end_clean();
                        break;
                    case 'java':
                        $launcher = new JavaLauncher();
                        $error = $launcher->launch($pluginFile, $arguments, $responseString);
                        break;
                    case 'exe':
                        $launcher = new ExeLauncher();
                        $error = $launcher->launch($pluginFile, $arguments, $responseString);
                        break;
                    default:
                        $error = "unsupported plugin type '$pluginType'";
                }
                chdir($cwd);
            }

            if (!$error)
            {
                if (isset($responseString))
                {
                    try {
                        $response = PluginResponse::fromXmlString($responseString);
                    }
                    catch (Exception $ex)
                    {
                        $response =PluginResponse::createError('Internal error. Plugin did not supply valid response XML and this error occurred: ' . $ex->getMessage() . '. Plugin instead supplied this response string: ' . $responseString);
                    }
                }
            }
            else
            {
                $response = PluginResponse::createError('Plugin cannot be launched (' . $error . ').');
            }

            $outputFile = $response->getOutput();
            if ($outputFile)
            {
                $outputFolder = Config::get('paths', 'output');

                $newFile = $outputFolder . date('Y-m-d_H-i-s_') . StringUtils::randomString(10) . '.zip';
                if (rename($outputFile, $newFile))
                {
                    $outputFile = $newFile;
                }
                else
                {
                    $outputFile = 'tmp-file-rename-failed';
                }
            }

            /**
             * @var $pluginTest \PluginTest
             * @var $submission \Submission
             * @var $previousSubmissions \Submission[]
             */
            if ($isTest)
            {
                $pluginTest = Repositories::findEntity(Repositories::PluginTest, $rowId);
                $pluginTest->setStatus(\PluginTest::STATUS_COMPLETED);
                $pluginTest->setSuccess($response->getFulfillment());
                $pluginTest->setInfo($response->getDetails());
                $pluginTest->setOutput($outputFile);
                Repositories::persistAndFlush($pluginTest);
            }
            else
            {
                $submission = Repositories::findEntity(Repositories::Submission, $rowId);
                // There is a sort of a race condition in here.
                // It is, in theory, possible, that there will be two submissions with the "latest" status after all is done
                // This should not happen in practice, though, and even if it does, it will have negligible negative effects.
                $previousSubmissions = Repositories::makeDqlQuery("SELECT s FROM \Submission s WHERE s.user = :sameUser AND s.assignment = :sameAssignment AND s.status != 'graded' AND s.status != 'deleted'")
                        ->setParameter('sameUser', $submission->getUser()->getId())
                        ->setParameter('sameAssignment', $submission->getAssignment()->getId())
                        ->getResult();
                foreach ($previousSubmissions as $previousSubmission)
                {
                    $previousSubmission->setStatus(\Submission::STATUS_NORMAL);
                    Repositories::getEntityManager()->persist($previousSubmission);
                }
                $submission->setStatus(\Submission::STATUS_LATEST);
                $submission->setInfo($response->getDetails());
                $submission->setSuccess($response->getFulfillment());
                $submission->setOutputfile($outputFile);
                Repositories::getEntityManager()->persist($submission);
                Repositories::flushAll();
            }

            return !$error;
        }
        catch (Exception $exception)
        {
            $errorInformation = "Internal error. Plugin launcher or plugin failed with an internal error. Exception information: " . $exception->getMessage() . " in " . $exception->getFile() . " at " . $exception->getLine();
            if ($isTest)
            {
                $pluginTest = Repositories::findEntity(Repositories::PluginTest, $rowId);
                $pluginTest->setStatus(\PluginTest::STATUS_COMPLETED);
                $pluginTest->setSuccess(0);
                $pluginTest->setInfo($errorInformation);
                Repositories::persistAndFlush($pluginTest);
            }
            else
            {
                $submission = Repositories::findEntity(Repositories::Submission, $rowId);
                $submission->setStatus(\Submission::STATUS_NORMAL);
                $submission->setInfo($errorInformation);
                Repositories::persistAndFlush($submission);
            }
            return false;
        }
	}

    /**
     * Launches plugin in detached process (asynchronous).
     * @param string $pluginType one of 'php', 'java', or 'exe'
     * @param string $pluginFile plugin file path
     * @param string $inputFile input file path
     * @param boolean $isTest true, if this is just a plugin test and not a submission
     * @param int $rowId ID of row to be updated
     * @param array $arguments plugin arguments
     * @throws Exception
     */
	public static function launchPluginDetached (
        $pluginType, $pluginFile, $inputFile,
			$isTest, $rowId, $arguments = array())
	{
		$launchPluginArguments = ShellUtils::quotePhpArguments(func_get_args());

        // Get config file and autoloader file
        $paths = Config::get('paths');
		$configFile = $paths['configFile'];
        $internalConfigFile = $paths['internalConfigFile'];
		$vendorAutoload = $paths['composerAutoload'];

        // This code will be passed, shell-escaped to the PHP CLI
		$launchCode = <<<LAUNCH_CODE
require_once '$vendorAutoload';
\asm\core\Config::init('$configFile', '$internalConfigFile');
\asm\utils\ErrorHandler::register();
\asm\core\Core::launchPlugin($launchPluginArguments);
LAUNCH_CODE;

		ShellUtils::phpExecInBackground(Config::get('bin', 'phpCli'), $launchCode);
	}

	/**
	 * Prints formatted response to UI request.
	 * @param UiResponse $response response data
	 */
	public static function sendUiResponse (UiResponse $response)
	{
		echo $response->toJson();
	}

    /**
     * Launches UI script (handler for request from UI).
     * @param array $data associative array with request arguments
     * @param array $files uploaded files
     * @throws CoreException when the first array is empty
     */
	public static function handleUiRequest (array $data, array $files = array())
	{

        if (empty($data))
		{
			throw new CoreException("No request data received");
		}

		$request = UiRequest::fromArray($data);

		self::$request = $request->getRequestName();

		$handler = $request->getHandler();
		$handler->run($request->getParams(), $files);
	}

	/**
	 * Creates and initializes logger instance if it doesn't exist yet.
	 */
	protected static function initLogger ()
	{
		if (!self::$logger)
		{
			$user = User::instance();
			$username = $user->isLogged() ? $user->getName() : '[not logged in]';
			$remoteAddr = ($_SERVER['REMOTE_ADDR'] != '::1')
					? $_SERVER['REMOTE_ADDR'] : '[localhost]';
			$remoteHost = isset($_SERVER['REMOTE_HOST'])
					? $_SERVER['REMOTE_ADDR'] : '[no lookup]';
			self::$logger = Logger::create(Config::get('paths', 'log'))
				->setMaxFileSize(2097152)	// 2 * 1024 * 1024
				->setMaxFileCount(5)
				->setEntrySeparator("\n\n")
				->setLineSeparator("\n")
				->setDatetimeFormat('Y-m-d H:i:s')
				->setHeader("User " . $username . ", IP " . $remoteAddr . ", host " . $remoteHost . ", request " . self::$request);
		}
	}

	/**
	 * Logs supplied error.
	 * @param Error $error
	 * @see logException()
	 */
	public static function logError (Error $error)
	{
		self::initLogger();
        self::$logger->log($error->toString());
	}

	/**
	 * Logs supplied exception.
	 * @param Exception $e
	 * @see logError()
	 */
	public static function logException (Exception $e)
	{
		self::logError(Error::create(Error::levelFatal, self::getCustomMessage($e),
				'Runtime error', self::getCustomTrace($e)));
	}

	/**
	 * Creates message with custom formatting from supplied exception.
	 * @param Exception $e
	 * @return string error message with format: \<message\> (\<file\>:\<line\>)
	 */
	protected static function getCustomMessage (Exception $e)
	{
		return StringUtils::stripFunctionLinks($e->getMessage()) . ' (' .
				basename($e->getFile()) . ':' . $e->getLine() . ')';
	}

	/**
	 * Creates stack trace with custom formatting from supplied exception.
	 * @param Exception $e
	 * @return string stack trace
	 */
	protected static function getCustomTrace (Exception $e)
	{
		$trace = array();
		$index = 0;
		$defaults = array(
			'file' => null,
			'line' => null,
			'class' => null,
			'function' => null,
			'type' => '::',
			'args' => array(),
		);

		foreach ($e->getTrace() as $props)
		{
			$props = array_merge($defaults, $props);
			$location = $props['file']
					? (($props['file'] != 'Command line code')
						? basename($props['file']) . ':' . $props['line']
						: '[command line]')
					: '[unknown location]';
			if (($props['function'] == 'trigger_error') && ($props['class'] === null))
			{
				$trace[] = "User error triggered ($location): {$props['args'][0]}";
			}
			elseif ((($props['class'] == 'Utils') && ($props['function'] == 'turnErrorToException'))
					|| (($props['class'] == 'ErrorHandler') && ($props['function'] == 'handleException'))
					|| (($props['class'] == 'ErrorHandler') && ($props['function'] == 'handleError')))
			{
				continue;
			}
			else
			{
				$arguments = ShellUtils::quotePhpArguments($props['args']);
				$function = $props['function'] ? "{$props['function']}($arguments)" : '';
				$caller = ($function && $props['class']) ? $props['class'] : '';
				$call = $caller ? $props['type'] : '';
				$trace[] = "#$index $location {$caller}{$call}{$function}";
				++$index;
			}
		}

		return implode("\n", $trace);
	}
}

