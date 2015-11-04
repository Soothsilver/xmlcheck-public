<?php

namespace asm\core;
use asm\utils\Filesystem;
use Exception;

/**
 * Provides access to application configuration (kept in separate INI file) @module.
 *
 * To use this class, it must be initialized using init() method. All configuration
 * properties are then accessible using virtual .
 *
 * Implemented as internal-singleton module.
 */
class Config
{
	/**
	 * Default separator of section name and key in folder structure variables
     */
	const defaultFolderDelimiter = '.';
	/**
	 * Name of the folder structure section (contains relationships between other
	 * configuration properties resolved to be resolved as part of initialization)
     */
	const folderStructureId = 'folderStructure';
	/**
	 * Key of custom folder delimiter (value with this key in folder structure
	 * section is used instead of @ref defaultFolderDelimiter if present)
	 */
	const folderDelimiterId = 'delimiter';

    /** @var Config */
	private static $instance;	///< singleton instance

    /**
     * Initializes this class' singleton instance from supplied INI files.
     * @param string[] $filenames paths to .ini files to be merged into configuration
     */
	public static function init (...$filenames)
	{
        // Load all specified .ini files
		self::$instance = new self($filenames);
	}


    /**
     * Gets the value of a configuration property with supplied section name and key (or whole section if no property given).
     *
     * @param string $section section name
     * @param string $property property to get, or null to get all properties
     * @throws Exception when Config::init was not called before
     * @return mixed property value (if @c $name is not null) or array with all
     *        properties from @c $section
     */
    public static function get($section, $property = null)
    {
        if (!self::$instance)
        {
            throw new Exception("Configuration is not initialized");
        }
        return self::$instance->_get($section, $property);
    }

    /**
     * Returns the web address of the running application.
     *
     * For example, this might return http://xmlcheck.projekty.ms.mff.cuni.cz/
     * @return string the web address of the running instance of XMLCheck
     * @throws Exception when Config::init was not called before
     */
    public static function getHttpRoot()
    {
        if (!self::$instance)
        {
            throw new Exception("Configuration is not initialized");
        }
        return self::$instance->_get("roots", "http");
    }

	/**
	 * Associative array with configuration properties.
	 * @var array
     */
	private $config;

    /**
     * Parses supplied INI files, merges them and initializes instance with extracted data.
     *
     * Section @ref folderStructureId is removed from data and used to turn partial
     * paths in configuration into full paths.
     * @param string[] $iniFiles paths to INI files
     * @throws Exception when there is no folderStructure section in any of the .ini files
     */
    private function __construct ($iniFiles)
	{
        $config = array();
        foreach($iniFiles as $iniFile)
        {
            $configFile = parse_ini_file($iniFile, true);
            $config = array_merge($config, $configFile);
        }
		// Hack: We are adding roots.web automatically.
		// Previously, the user had to specify in the config.ini file so that local files could be found.
		// However, that was too much of a hassle when moving installations and source code between two development
		// machines and the production environment. Therefore, we now detect this path automatically.
		$config["roots"]["web"] = realpath(Filesystem::combinePaths(__DIR__, "..", ".."));

        if (isset($config[self::folderStructureId]))
        {
            $folderStructure = $config[self::folderStructureId];
            unset($config[self::folderStructureId]);

            $delimiter = self::defaultFolderDelimiter;
            if (isset($folderStructure[self::folderDelimiterId]))
            {
                $delimiter = $folderStructure[self::folderDelimiterId];
                unset($folderStructure[self::folderDelimiterId]);
            }

            $config = $this->resolvePaths($config, $folderStructure, $delimiter);
        }
        else
        {
            throw new Exception("No 'folderStructure' section found in any of the loaded INI files.");
        }
        $this->config = $config;
	}


	/**
	 * Resolves supplied partial path using supplied base.
	 * @param string $parent base for @c $child
	 * @param string $child partial path to be resolved
	 * @return string absolute path of @c $child appended to @c $parent with
	 *        OS-dependent directory separators replaced by UNIX-style slashes (or
	 * @throws Exception when the combined paths don't point to an actual file on the filesystem
	 */
    private function resolvePath ($parent, $child)
	{

		$realPath = realpath(Filesystem::combinePaths($parent , $child));
		if ($realPath !== false)
		{
			$realPath = str_replace('\\', '/', $realPath);
			return (is_dir($realPath) ? $realPath . '/' : $realPath);
		}
		else
		{
			throw new Exception("The parent path '{$parent}' and the child path '{$child}' combined do not point to any file on the filesystem. Perhaps your internal.ini file is wrong?'");
		}
	}

    /**
     * Resolves parent-child relationships in configuration using supplied data.
     * @param array $config unresolved configuration
     * @param array $folderStructure parent-child key-value pairs
     * @param string $delimiter separator of section names and property keys
     * @throws Exception when some properties or sections referenced by folderStructure values are not present in any .ini file
     * @return array configuration with relationships resolved
     */
    private function resolvePaths ($config, $folderStructure, $delimiter)
	{
		foreach ($folderStructure as $parent => $children)
		{
			list($parentSection, $parentName) = explode($delimiter, $parent);
			if (!isset($config[$parentSection][$parentName]))
			{
                throw new Exception ("Property {$parentName} within section {$parentSection} was expected, but not found in any .ini file.");
			}

			foreach ($children as $child)
			{
				list($childSection, $childName) = explode($delimiter, $child);
				if (!isset($config[$childSection]))
				{
                    throw new Exception("Section {$childSection} expected, but not found in any .ini file.");
				}

				if (isset($childName))
				{
					if (isset($config[$childSection][$childName]))
					{
						$config[$childSection][$childName] = $this->resolvePath(
								$config[$parentSection][$parentName], $config[$childSection][$childName]);
					}
                    else
                    {
                        throw new Exception("Property {$childName} within section {$childSection} was expected, but not found in any .ini file.");
                    }
				}
				else
				{
					foreach ($config[$childSection] as $key => $val)
					{
						$config[$childSection][$key] = $this->resolvePath(
								$config[$parentSection][$parentName], $val);
					}
				}
			}
		}

		return $config;
	}

	/**
	 * Gets configuration property with supplied section name and key (or whole section).
	 * @param string $section section name
	 * @param mixed $name property key (string) or null
	 * @return mixed property value (if @c $name is not null) or array with all
	 *		properties from @c $section
	 */
    private function _get ($section, $name = null)
	{
		if ($name === null)
		{
			return (isset($this->config[$section]) ? $this->config[$section] : null);
		}
		
		return (isset($this->config[$section][$name]) ? $this->config[$section][$name] : null);
	}
}

