<?php

namespace asm\core;
use asm\core\lang\Language;
use asm\core\lang\StringID;
use asm\utils\Filesystem, asm\utils\Validator;

/**
 * Handler of core requests from UI.
 *
 * Request handlers are used by calling their run() method and their main handling
 * logic should be contained in their implementation of body() method. In case of
 * error, body() should set error using one of stop methods and return false.
 *
 * Calling request handler should be the final action of request handling script,
 * as the handler manages output as well.
 *
 * Methods tagged as [stopping] call appropriate stop method in case of error,
 * therefore no additional stop() calls should be made in case they fail (return false).
 */
abstract class UiScript
{
	/**
	 * Errors generated during the execution of the script.
	 * @var Error[]
     */
	private $errors = array();
	/**
	 * True, if the script failed to finish successfully.
	 * @var bool
     */
	private $failed = false;
	/**
	 * An associative array of script arguments.
	 * @var array
	 */
    private $params = array();

	/**
	 * Checks whether the active user is authorized to manage the specified lecture.
	 * @param \Lecture $lecture The lecture the active user wants to manage.
	 * @return bool True, if the user is authorized.
     */
	protected function authorizedToManageLecture(\Lecture $lecture)
	{
		$user = User::instance();
		if ($user->hasPrivileges(User::lecturesManageAll)) { return true; }
		if ($user->hasPrivileges(User::lecturesManageOwn && $lecture->getOwner()->getId() === User::instance()->getId())) { return true; }
		return false;
	}

    /**
     * Runs the request handler with supplied arguments.
     *
     * Handler is initialized first using init() override and in case of no errors,
     * body() override is run. After it finishes (whether successfully or not), errors are
     * logged and output is sent using output() override.
     * @param array $params associative array of script arguments
     * @param array $files associative array with info about files uploaded with request
     * @see init()
     * @see body()
     * @see output()
     */
    public final function run(array $params = array(), array $files = array())
    {
        $this->init($params, $files);

        if (!$this->isFailed())
        {
            $this->body();
        }

        $this->logErrors();
        $this->output();
    }

    /**
     * Convenience method for adding error and stopping script execution at the same time.
     *
     * Typical use in script body:
     * @code
     * if (($error = foo()) !== false)
     *        return $this->stop($error);
     * @endcode
     * @param mixed  $cause code of error cause (int) or cause message (string) if it doesn't have own code
     * @param string $effect error effect
     * @param string $details additional error info
     * @return bool false
     * @see addError()
     * @see stopDb()
     * @see stopRm()
     */
    protected final function stop($cause = null, $effect = null, $details = null)
    {
        $this->addError(Error::levelError, $cause, $effect, $details);
        return false;
    }

	/**
	 * Adds an error with the message based on the given StringID.
	 *
	 * @param int $stringID The StringID of the message to show to the user.
	 * @return bool Always returns false.
	 */
    protected final function death($stringID)
    {
        $this->addError(Error::levelError, Language::get($stringID));
        return false;
    }

    /**
     * Joins two error details strings together with a newline in between.
     * @param string $details1 error details
     * @param string $details2 error details
     * @return string joined error details
     */
    protected final function joinDetails($details1, $details2)
    {
        $details = array();
        if ($details1)
        {
            array_push($details, $details1);
        }
        if ($details2)
        {
            array_push($details, $details2);
        }
        return implode("\n", $details);
    }

    /**
     * Sets request handler arguments to be accessible by getParams().
     * @param array $params associative array of script arguments
     * @see getParams()
     */
    protected function setParams($params)
    {
        $this->params = $params;
    }

	/**
	 * Returns true if a parameter with the given name exists. This is useful for checkboxes: A browser sends information about a checkbox if and only if the user checked the checkbox.
	 * @param string $paramName Name of the parameter.
	 * @return bool Does the parameter exist?
     */
	protected function paramExists($paramName){
        return array_key_exists($paramName, $this->params);
    }

	/**
	 * Gets script argument with supplied key or all arguments.
	 *
	 * It is not recommended to use "null" as an argument. Previously, this was used for the "extract" function but using the extract function,
	 * even in the limited way it was used, is unsafe, and confuses IDEs.
	 *
	 * @param mixed $key argument key (string), array with argument keys, or null
	 *		to get all arguments
	 * @return mixed argument value if single key is supplied, otherwise associative
	 *		array with arguments (either all or with selected keys only)
	 * @see setParams()
	 */
	protected final function getParams ($key = null)
	{
		if ($key === null)
		{
			return $this->params;
		}
		elseif (!is_array($key))
		{
			return (isset($this->params[$key]) ? $this->params[$key] : null);
		}
		else
		{
			$ret = array();
			foreach ($key as $k)
			{
				$ret[$k] = $this->getParams($k);
			}
			return $ret;
		}
	}

	/**
	 *	[stopping]
	 * @param string $id file ID
	 * @return mixed file info (array) or false in case of error
	 */
	private function getUploadedFileInfo ($id)
	{
		$fileInfo = UploadManager::instance()->retrieve($id);

		if ($fileInfo === UploadManager::fileNotFound)
			return $this->stop('file has been lost', 'cannot retrieve uploaded file');

		if ($fileInfo === UploadManager::idNotSet)
		{
			if (!isset($_FILES[$id]) || ($_FILES[$id]['error'] != UPLOAD_ERR_OK)
					|| (!is_uploaded_file($_FILES[$id]['tmp_name'])))
			{
				return $this->stop(ErrorCode::upload, 'cannot retrieve uploaded file');
			}

			$file = $_FILES[$id];
			return array(
				'name' => $file['name'],
				'type' => $file['type'],
				'path' => $file['tmp_name'],
			);
		}
		return $fileInfo;
	}

	/**
	 * Gets path to pre-uploaded file with supplied ID [stopping].
	 * @param string $id file ID
	 * @return mixed file path (string) or false in case of error
	 * @see saveUploadedFile()
	 * @see UploadManager
	 */
	protected final function getUploadedFile ($id)
	{
		if (!($fileInfo = $this->getUploadedFileInfo($id)))
			return false;

		return $fileInfo['path'];
	}

	/**
	 * @param $id
	 * @return bool|string
	 */
	protected final function getUploadedFileName ($id)
	{
		if (!($fileInfo = $this->getUploadedFileInfo($id)))
			return false;

		return $fileInfo['name'];
	}

	/**
	 * Save pre-uploaded file to permanent storage [stopping].
	 * @param string $id file ID
	 * @param string $destination destination to which the file is to be moved
	 * @return bool success
	 * @see getUploadedFile()
	 * @see UploadManager
	 */
	protected final function saveUploadedFile ($id, $destination)
	{
		$src = $this->getUploadedFile($id);
		if (!$src)
			return false;

		if (!rename($src, Filesystem::realPath($destination)))
			return $this->stop(Language::get(StringID::UploadUnsuccessful));

		return true;
	}

	/**
	 * Stores error (warning, notice) to be appended to script output.
	 *
	 * Sets @ref $failed flag to true if error is level is Error::levelError or higher.
	 * @param int $level error severity (@ref Error "Error::level*" constant)
	 * @param mixed $cause error cause code (int) or message (string)
	 * @param string $effect error effect
	 * @param string $details additional error info
	 * @see getErrors()
	 * @see clearErrors()
	 */
	protected final function addError ($level, $cause, $effect = null, $details = null)
	{
		$this->errors[] = Error::create($level, $cause, $effect, $details);
		if ($level >= Error::levelError)
		{
			$this->failed = true;
		}
	}

	/**
	 * Gets stored errors.
	 * @return array errors (Error instances)
	 * @see addError()
	 * @see clearErrors()
	 * @see isFailed()
	 */
	protected final function getErrors ()
	{
		return $this->errors;
	}

	/**
	 * Checks whether this handler is flagged as failed.
	 * @return bool true if handler is failed
	 */
	protected final function isFailed ()
	{
		return $this->failed;
	}

	/**
	 * Clears all stored errors and unsets @ref $failed flag.
	 * @return bool true if the handler was flagged as failed
	 * @see addError()
	 * @see getErrors()
	 * @see isFailed()
	 */
	protected final function clearErrors ()
	{
		$failed = $this->isFailed();
		$this->errors = array();
		$this->failed = false;
		return $failed;
	}

	/**
	 * Logs all errors added during handler execution to system log.
	 * @return UiScript self
	 * @see Core::logError()
	 */
	private function logErrors ()
	{
		foreach ($this->errors as $error)
		{
			Core::logError($error);
		}
		return $this;
	}

	/**
	 * Outputs supplied data along with stored errors to UI.
	 * @param array $data data to output
	 */
	protected final function outputData ($data = array())
	{
		Core::sendUiResponse(UiResponse::create($data, $this->getErrors()));
	}

	/**
	 * Checks whether required handler arguments are set [stopping].
	 * @param mixed $args array with argument keys or single argument key string
	 * @param string [...] argument keys can be specified as method arguments
	 * @return bool true if arguments for all supplied keys are set
	 * @see isInputValid()
	 */
	protected final function isInputSet ($args)
	{
		if (is_string($args)) {
			$args = (func_num_args() > 1) ? func_get_args() : array($args);
		}

		$missingArgs = array();
		foreach ($args as $arg)
		{
			if (!isset($this->params[$arg]))
			{
				$missingArgs[] = $arg;
			}
		}

		if (!empty($missingArgs))
		{
			return $this->stop(ErrorCode::inputIncomplete, null,
					'Missing input fields: ' . implode(', ', $missingArgs) . '.');
		}
		return true;
	}

	/**
	 * Checks whether required handler arguments are set and fit supplied constraints [stopping].
	 * @param array $fields associative array of fields and their validation filters
	 *	@code
	 *	array(
	 *		'<argument name>' => array(\<FILTER\>, ...),
	 *		[...]
	 *	)
	 *	@endcode
	 *	where \<FILTER\> is either filter name string (must be accepted by Validator::validate()
	 * as second argument) or array key-value pair with filter name as key and
	 * filter options array as value, e.g.:
	 *	@code
	 *	array(
	 *		'id' => array('isId'),
	 *		'name' => array(
	 * 		'isAlphaNumeric',
	 * 		'hasLength' => array(
	 * 			'min_length' => 5,
	 * 			'max_length' => 15,
	 * 		),
	 *		),
	 *	)
	 *	@endcode
	 * @return bool true if arguments for all supplied keys are set and valid to supplied constraints
	 * @see isInputSet()
	 * @see Validator
	 */
	protected final function isInputValid ($fields)
	{
		if (!$this->isInputSet(array_keys($fields)))
			return false;

		foreach ($fields as $name => $filters)
		{
			if ($filters === null)
			{
				continue;
			}
			if (!is_array($filters))
			{
				$filters = array($filters => array());
			}
			foreach ($filters as $filter => $options)
			{
				if (is_int($filter))
				{
					$filter = $options;
					$options = array();
				}
				$details = Validator::validate($this->getParams($name), $filter, $options);
				if ($details)
				{
					if ($details === true)
					{
						return $this->stop(ErrorCode::inputInvalid, null, "key: '$name'");
					}
					else
					{
						return $this->stop(ErrorCause::invalidInput($details, $name));
					}
				}
			}
		}
		return true;
	}

	/**
	 * Checks whether user has at least one the given sets of privileges [stopping].
	 * @param int $privileges sets of privileges to check against
	 * @return bool true if he has
	 * @see User::hasPrivileges()
	 */
	protected final function userHasPrivileges (...$privileges)
	{
		$reason = "";
        if (!User::instance()->isSessionValid($reason))
        {
            return $this->stop(Language::get(StringID::SessionInvalidated));
        }
		if (!User::instance()->hasPrivileges(...$privileges))
		{
            return $this->stop(Language::get(StringID::InsufficientPrivileges));
		}
		return true;
	}

	/**
	 * Initializes handler with supplied arguments.
	 *
	 * Should be overridden and finalized in abstract descendants, not in final handlers.
	 * Script body() is executed only if this method doesn't stop().
	 * @param array $params associative array of script arguments supplied to run() on handler execution
	 * @param array $files associative array with info about files uploaded with request supplied to run() on handler execution
	 */
	protected abstract function init (array $params = array(), array $files = array());

	/**
	 * Contains main handling logic specific to each handler.
	 *
	 * Should be overridden in final handler classes.
	 */
	protected abstract function body ();

	/**
	 * Outputs appropriately formatted response to UI.
	 *
	 * Should be overridden and finalized in abstract descendants, not in final handlers.
	 */
	protected abstract function output ();
}

