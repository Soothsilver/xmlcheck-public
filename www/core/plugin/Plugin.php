<?php

namespace asm\plugin;
use asm\utils\Filesystem, asm\utils\Compression, Exception;

/**
 * Base for all PHP plugin classes for @projectname plugins.
 *
 * All PHP plugins for @projectname must be descendants of this class (they
 * will not be launched otherwise). They are launched using this class' run()
 * method, handles some tasks common to all plugins (such as unpacking input and
 * packing output or error handling). Descendants must provide implementation
 * of setUp() and execute() methods.
 *
 * Input and output is not accessible directly to plugins, instead this class
 * provides methods providing such access.
 *
 * Plugin criteria (which are returned as the results of plugin run) are also
 * inaccessible directly and must be managed using this class' methods.
 */
abstract class Plugin
{
	protected $dataFolder = null;	///< temporary folder containing input data
	private $outputFolder = null;	///< temporary folder for plugin output
	private $results = array();	///< plugin criteria
	private $sources = array();	///< input file text cache

	public function __construct () {}

	/**
	 * Initializes plugin using supplied arguments.
	 * @param array $params plugin arguments
	 * @see execute()
	 * @see run()
	 */
	protected abstract function setUp ($params);

	/**
	 * Checks input data and update plugin criteria with results (main plugin logic).
	 * @see setUp()
	 * @see run()
	 */
	protected abstract function execute ();

	/**
	 * Runs plugin with supplied arguments.
	 *
	 * @li First element in @c $args must be a path to ZIP archive with input data.
	 * @li Plugin execution is stopped on all triggered errors or uncaught exceptions
	 *		and plugin error is returned.
	 * @li setUp() and execute() methods are run respectively after input is
	 *		unpacked and output folder is created.
	 * @li Plugin creates and later removes two temporary folders. It can also create
	 *		one ZIP archive with plugin output (path is returned in plugin results).
	 * 
	 * @param array $args simple array with plugin arguments
	 * @return PluginResponse plugin results or error
	 */
	public final function run (array $args)
	{
		set_error_handler(array('asm\utils\Utils', 'turnErrorToException'));

        $cwd = getcwd();
		try
		{
           if (($args == null) || (count($args) < 1))
			{
				throw new PluginUseException('Data file argument missing');
			}

			$this->dataFolder = Filesystem::tempDir();
			$dataFile = array_shift($args);
			if (!Compression::unzip($dataFile, $this->dataFolder, $unzipMessage))
            {
               $response = PluginResponse::createError("ZIP extraction failed (" . $unzipMessage . ").\n\nPerhaps you did not submit a ZIP file " .
               "or that file was corrupted during upload. You may try again. Extraction was attempted to folder " .
               str_replace( "\\", "/",$this->dataFolder ));
            }
            else
            {
                $this->outputFolder = Filesystem::tempDir();

                chdir($this->dataFolder);

                $this->setUp($args);
                $this->execute();

                chdir($cwd);

                $outputFile = $this->packOutput();
                $outputPath = ($outputFile != null) ? realpath($outputFile) : null;

                $response = PluginResponse::create($this->results, $outputPath);
            }
		}
		catch (PluginException $e)
		{
			$response = PluginResponse::createError($e->getMessage());
		}
		catch (Exception $e)
		{
			$response = PluginResponse::createError('Runtime error: ' . $e->getMessage() . " (file " . $e->getFile() . ", line " . $e->getLine());
		}
        // If an exception occurred during $this->setUp or $this->execute, we must still change the current directory back,
        // in case more plugins are to be run (in a test case battery)
        chdir($cwd);

		restore_error_handler();

		if ($this->dataFolder != null)
		{
			Filesystem::removeDir($this->dataFolder);
		}
		if ($this->outputFolder != null)
		{
			Filesystem::removeDir($this->outputFolder);
		}

		return $response;
	}

	/**
	 * Gets absolute path for supplied relative source file path.
	 * @param string $path file path relative to input folder
	 * @return string absolute source file path
	 * @see makeFullSources()
	 * @see getOutputFile()
	 */
	protected final function getSourceFile ($path)
	{
		return $this->dataFolder . DIRECTORY_SEPARATOR . $path;
	}

	/**
	 * Replaces supplied relative source file paths with absolute paths.
	 * @param array $sources array with source file paths relative to input folder
	 *        (keys will be preserved)
	 * @return array array of absolute paths
	 * @see getSourceFile()
	 * @see getOutputFile()
	 */
	protected final function makeFullSources ($sources)
	{
		foreach ($sources as $key => $val)
		{
			$sources[$key] = $this->getSourceFile($val);
		}
		return $sources;
	}

	/**
	 * Gets absolute path for supplied relative output file path.
	 * @param string $path file path relative to output folder
	 * @return string absolute output file path
	 * @see getSourceFile()
	 */
	protected final function getOutputFile ($path)
	{
		return $this->outputFolder . DIRECTORY_SEPARATOR . $path;
	}

	/**
	 * Packs contents of output folder into ZIP archive.
	 * @return mixed path (string) of ZIP archive with packed output, or null if
	 *		no output files were created during plugin execution
	 */
	private function packOutput ()
	{
		$outputFile = null;
		if (($this->outputFolder != null) && (is_dir($this->outputFolder))
				&& (count(glob($this->outputFolder . DIRECTORY_SEPARATOR . '*')) > 0))
		{
			$outputFile = tempnam(sys_get_temp_dir(), '');
			if (!Compression::zip($this->outputFolder, $outputFile))
			{
				$outputFile = null;
			}
		}
		return $outputFile;
	}

	/**
	 * Replaces path to input folder with "\." in supplied string.
	 *
	 * Input folder is just temporary, therefore its path should not appear in
	 * any info/error messages returned by plugin.
	 * @param string $str
	 * @return string @c $str with source file paths made relative
	 */
	private function clipPaths ($str)
	{
		if ($this->dataFolder != null)
		{
			$realDataFolderPath = realpath($this->dataFolder);
			$str = str_ireplace($realDataFolderPath, '.', $str);
			$str = str_ireplace(str_replace(\DIRECTORY_SEPARATOR, '/', $realDataFolderPath),
					'.', $str);
		}
		return $str;
	}

	/**
	 * Add new criterion with supplied name to plugin results.
	 * @param string $name criterion name (unique, descriptive)
	 * @throws PluginCodeException in case criterion with @c $name already exists
	 * @see updateCriterion()
	 */
	protected final function addCriterion ($name)
	{
		if (isset($this->results[$name]))
		{
			throw new PluginCodeException('Cannot add criterion with same name twice ('
					. $name . ')');
		}
		$this->results[$name] = array(
			'passed' => false,
			'fulfillment' => 0,
			'details' => '',
		);
	}

	/**
	 * Update plugin results criterion with supplied data.
	 * @param string $name criterion name
	 * @param bool $passed whether to mark criterion as passed
	 * @param int $fulfillment fulfillment percentage
	 * @param string $details additional info
	 * @throws PluginCodeException in case criterion with @c $name doesn't exist
	 * @see passCriterion()
	 * @see failCriterion()
	 */
	protected final function updateCriterion ($name, $passed, $fulfillment, $details = '')
	{
		if (!isset($this->results[$name]))
		{
			throw new PluginCodeException('Criterion ' . $name . ' doesn\'t exist');
		}

		$criterion = &$this->results[$name];

		$passed = (bool)$passed;
		$criterion['passed'] = $passed;

		if (!is_numeric($fulfillment))
		{
			$fulfillment = $passed ? 100 : 0;
		}
		else
		{
			$fulfillment = (int)$fulfillment;
			if ($fulfillment < 0)
			{
				$fulfillment = 0;
			}
			elseif ($fulfillment > 100)
			{
				$fulfillment = 100;
			}
		}
		$criterion['fulfillment'] = $fulfillment;

		$criterion['details'] = $this->clipPaths($details);
	}

	/**
	 * Mark criterion as passed.
	 * @param string $name criterion name
	 * @param int $fulfillment fulfillment percentage
	 * @see failCriterion()
	 * @see updateCriterion()
	 */
	protected final function passCriterion ($name, $fulfillment = 100)
	{
		$this->updateCriterion($name, true, $fulfillment);
	}

	/**
	 * Mark criterion as failed and add supplied failure info.
	 * @param string $name criterion name
	 * @param string $details failure info
	 * @param int $fulfillment fulfillment percentage
	 * @see passCriterion()
	 * @see updateCriterion()
	 */
	protected final function failCriterion ($name, $details, $fulfillment = 0)
	{
		$this->updateCriterion($name, false, $fulfillment, $details);
	}

	/**
	 * Throw appropriate exception if some mandatory plugin arguments are missing.
	 * @param array $params arguments supplied to plugin
	 * @param array $descriptions descriptions of expected arguments
	 * @throws PluginException in case @c $params is an array (not null) and contains
	 *		less elements than @c $descriptions (exception message contains argument
	 *		descriptions)
	 */
	protected function requireParams (array $params, array $descriptions)
	{
		$descriptionCount = count($descriptions);
		if (($descriptions === null) || ($descriptionCount == 0))
		{
			return;
		}

		if (($params === null) || (count($params) < $descriptionCount))
		{
			throw new PluginUseException('Plugin takes ' . $descriptionCount
					. ' mandatory arguments: ' . implode(', ', $descriptions));
		}
	}

	/**
	 * Gets line from text source file.
	 * @param string $source source file path
	 * @param int $line line number
	 * @return string line @c $line from source file
	 */
	protected function getSourceLine ($source, $line)
	{
		if (!isset($this->sources[$source]))
		{
			$contents = file_get_contents($source);
			$contents = str_replace("\r\n", "\n", $contents);
			$contents = str_replace("\r", "\n", $contents);
			$this->sources[$source] = explode("\n", $contents);
		}
		return $this->sources[$source][$line - 1];
	}
}

