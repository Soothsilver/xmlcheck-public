<?php

namespace asm\utils;

/**
 * Filesystem-related utility functions.
 */
class Filesystem
{
    /**
     * Gets the filenames of all traditional simple files, in the specified directory.
     *
     * Sample use:
     * @code
     * Filesystem::getFiles("hello")
     * @endcode
     * could return, for example,
     * @code
     * Array (
     *  [0] => "hello.txt",
     *  [1] => "super.txt"
     * )
     * @endcode
     * @param $directory string the directory to list files of
     * @return string[] all filenames
     */
    public static function getFiles($directory)
    {
        $scanned_directory = array_diff(scandir($directory), ['..', '.']);
        foreach ($scanned_directory as $key => $scannedFile) {
            if (is_dir(static::combinePaths($directory, $scannedFile)))
            {
                unset($scanned_directory[$key]);
            }
        }
        $scanned_directory = array_values($scanned_directory);
        return $scanned_directory;
    }

    /**
     * Combines all paths given as arguments into a single one.
     * (Transforms all backslashes into forward slashes. There will be no slash at the end of the resultant path,
     * unless the result is the single character '/'. Multiple consecutive slashes are rolled into one only.)
     *
     * Source: http://stackoverflow.com/a/15575293/1580088
     *
     * Example: [ 'abc', 'def' ] turns into 'abc/def'
     * Example: [ 'abc/', '/def/' ] turns into 'abc/def'
     * Example: [ '', '' ] turns into ''
     * Example: [ '', '/' ] turns into '/'
     * Example: [ '/', '/a' ] turns into '/a'
     * Example: [ '/abc' ,'def' ] turns into '/abc/def'
     * Example: [ '', 'foo.jpg' ] turns into 'foo.jpg'
     * Example: [ 'dir', '0', 'a.jpg' ] turns into 'dir/0/a.jpg'
     * Example: [ 'C:\long\path\', '/shortfile' ] turns into 'C:/long/path/shortfile'
     * @param string ...$fragments path fragments to join
     * @return string the combined paths
     */
    public static function combinePaths(...$fragments)
    {
        $paths = [];
        /** @var string[] $fragments */
        foreach ($fragments as $arg) {
            if ($arg !== '') { $paths[] = str_replace('\\', '/', $arg); }
        }
        $result = preg_replace('#/+#','/',join('/', $paths));
        if (strlen($result) > 1 && substr($result, -1) === '/')
        {
            $result = substr($result, 0, -1);
        }
        return $result;
    }
    /**
     * Copies the source file or contents of the source folder into the destination folder.
     *
     * Note: This will create the destination folder if it does not exist.
     * Note: If some files fail to copy, all other files will still be copied and the function will return true.
     * @param $sourceFileOrFolder string the file or folder to copy
     * @param $destinationFolder string where to copy the files to
     * @return bool success?
     */
    public static function copyIntoDirectory( $sourceFileOrFolder, $destinationFolder )
    {
        // Remove trailing slashes and change into forward slashes.
        $sourceFileOrFolder = self::combinePaths($sourceFileOrFolder);
        $destinationFolder = self::combinePaths($destinationFolder);

        if( is_dir($sourceFileOrFolder) )
        {
            if (!file_exists($destinationFolder))
            {
                if (!self::createDir($destinationFolder, 0777))
                {
                    return false;
                }
            }
            else
            {
                if (!is_dir($destinationFolder))
                {
                  return false;
                }
            }
            $objects = scandir($sourceFileOrFolder);
            if( count($objects) > 0 )
            {
                foreach( $objects as $file )
                {
                    if( $file == "." || $file == ".." )
                    {
                        continue;
                    }
                    $sourceFile = self::combinePaths($sourceFileOrFolder, $file);
                    $targetFile = self::combinePaths($destinationFolder, $file);
                    if( is_dir( $sourceFile ) )
                    {
                        self::copyIntoDirectory( $sourceFile, $targetFile );
                    }
                    else
                    {
                        copy( $sourceFile, $targetFile );
                    }
                }
            }
            return true;
        }
        elseif( is_file($sourceFileOrFolder) )
        {
            if (!file_exists($destinationFolder))
            {
                if (!self::createDir($destinationFolder, 0777))
                {
                    return false;
                }
            }
            else
            {
                if (!is_dir($destinationFolder))
                {
                    return false;
                }
            }
            return copy($sourceFileOrFolder, self::combinePaths($destinationFolder, basename($sourceFileOrFolder)));
        }
        else
        {
            return false;
        }
    }
    /**
	 * Gets absolute path from supplied relative path. Contrary to PHP realpath, this function will not fail
     * if the last path component does not exist. The absolute path won't have a trailing slash.
	 * @param string $path a relative path
	 * @return string absolute path, or false if the directory before the last component does not exist
	 */
	public static function realPath ($path)
	{
        $dirPath = realpath(dirname($path));
        if ($dirPath === false)
        {
            return false;
        }
        return self::combinePaths($dirPath, basename($path));
	}

	/**
	 * Creates a directory at the path, even recursively creating directories. If the directory already exists, it changes its access mode.
	 * @param string $path folder path
	 * @param int $mode unix permissions to set
     * @return bool success?
	 */
	public static function createDir ($path, $mode = 0777)
	{
		if (!file_exists($path))
        {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            return @mkdir($path, $mode, true);
		}
		else if (is_dir($path))
		{
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            return @chmod($path, $mode);
		}
        else {
            return false;
        }
	}

    /**
     * Creates a file, creating parent directories if needed, and puts the string into it.
     * @param string $filename file path to create
     * @param string $string string to save to file
     * @param int    $mode unix permissions
     * @return bool success?
     */
	public static function stringToFile ($filename, $string, $mode = 0777)
	{
		$directoryName = dirname($filename);
		self::createDir($directoryName, $mode);
		return (file_put_contents($filename, $string) !== false);
	}

	/**
	 * Deletes folder and its contents. If it's a regular file or symlink, it is deleted anyway.
     *
     * Note: This will attempt to change its permission mode to 0777 in order for the deletion to work if needed.
     *
	 * @param string $dir folder to be deleted
	 * @return bool true if folder was successfully deleted
	 */
	public static function removeDir ($dir)
	{
		if (!file_exists($dir)) return true;
		if (!is_dir($dir) || is_link($dir)) return unlink($dir);
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') continue;
            $thisItem = self::combinePaths($dir, $item);
			if (!self::removeDir($thisItem)) {
				 chmod($thisItem, 0777);
				 if (!self::removeDir($thisItem)) return false;
			}
        }
        return rmdir($dir);
	}

	/**
	 * Deletes regular file, if it exists.
     *
     * Note: Does not generate warnings. If the file had wrong permissions, this attempts to set them to 0777 prior to deletion.
	 * @param string $path file to be deleted
	 * @return bool true if file was deleted successfully
	 */
	public static function removeFile ($path)
	{
		if (!is_file($path)) return true;
		if (unlink($path)) return true;
		else
		{
			chmod($path, 0777);
			return unlink($path);
		}
	}

	/**
	 * Creates a temporary folder and return the path to it.
     *
     * Note: This path is not necessarily absolute.     *
	 * @param mixed $dir parent folder of the temporary folder or null to create it as a subfolder of system-wide temporary folder.
	 * @param string $prefix folder name prefix, that will be suffixed by a random number
	 * @param int $mode unix privileges
	 * @return string temporary folder path
	 */
	public static function tempDir ($dir = null, $prefix = '', $mode = 0700)
	{
		if (!is_dir($dir))
		{
			$dir = sys_get_temp_dir();
		}

		$dir = self::combinePaths($dir) . "/";

		do
		{
			$path = $dir . $prefix . mt_rand(0, 9999999);
		}
        while (!self::createDir($path, $mode));

		return $path;
	}
}

