<?php

namespace asm\utils;
use ZipArchive, RecursiveIteratorIterator, RecursiveDirectoryIterator;

/**
 * Functions for ZIP compression and decompression.
 */
class Compression
{
	/**
	 * Pack source file or folder to a ZIP archive
     *
	 * @param string $source source file/folder path
	 * @param string $destination destination file path; the file must not already exist
	 * @return bool true on success
	 */
	public static function zip ($source, $destination)
	{
		$source = realpath($source);
		if (!file_exists($source))
		{
			return false;
		}

		$zip = new ZipArchive();
		if ($zip->open($destination, ZipArchive::CREATE) !== true)
		{
			return false;
		}

		if (is_dir($source))
		{
			$files = new RecursiveIteratorIterator(
					new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($files as $file)
			{
                // See https://bugs.php.net/bug.php?id=67996
                // and http://stackoverflow.com/questions/22020437/ziparchive-cant-unzip-files-that-ziparchive-zips-in-php/25765761#25765761
                if (basename($file) === ".") { continue;}
                if (basename($file) === "..") { continue; }
                if (strpos($file, "/../") !== false || strpos($file, "../") !== false)
                {
                    return false;
                }

				$file = realpath($file);
				if (is_dir($file))
				{
                    $dirName = substr(str_replace($source . DIRECTORY_SEPARATOR,
                        '', $file . DIRECTORY_SEPARATOR), 0, -1);
                    if ($dirName !== false)
                    {
                        $zip->addEmptyDir($dirName);
                    }
				}
				elseif (is_file($file))
				{
					$zip->addFile($file, str_replace($source . DIRECTORY_SEPARATOR, '', $file));
				}
			}
		}
		elseif (is_file($source))
		{
			$zip->addFile($source, basename($source));
		}

		return $zip->close();
	}

	/**
	 * Unpack ZIP archive to specified folder.
     * If it contains only a single directory and nothing else, its contents are extracted instead of the entire ZIP file. This also happens if the only other directory is the "__MACOSX" metadata folder that Macintosh operating systems add to generated ZIP files.
     *
     * This function is a security vulnerability. Possible attacks include a very large ZIP file, such as a ZIP bomb, or putting in a file with a relative path such as '../etc/passwd'.
     *
     *
	 * @param string $archive source archive path
	 * @param string $destination destination folder path
     * @param [out]string $errorMessage why did the extraction fail?
	 * @return true on success
	 */
	public static function unzip ($archive, $destination, &$errorMessage = null)
	{
		$zip = new ZipArchive();
        $errorCode = $zip->open($archive);
		if ($errorCode !== true)
		{
            $errorMessage = "could not open ZIP file (error code " . $errorCode . ")";
			return false;
		}

        // Otherwise, this is a normal ZIP file.
		if (!$zip->extractTo($destination))
		{
			$zip->close();
            $errorMessage = "extraction failed";
			return false;
		}
        $zip->close();

        // Now, we'll check if the ZIP file contains only a single folder. If so,
        // then we'll copy its contests to the root temporary folder, then remove the original folder.
        $files = scandir($destination);
        if ($files === false)
        {
            $errorMessage = "scanning the temporary directory failed";
            return false;
        }
        // On Linux, scandir returns the "." and ".." pseudofolders we are not interested in
        $files = array_diff($files, [ ".", ".." ]);

        // For ZIP files generated on Mac OS X, we are not interested in the metadata folder.
        $files = array_diff($files, [ "__MACOSX" ]);

        if (count($files) === 0)
        {
            $errorMessage = "the ZIP file is empty";
            return false;
        }
        elseif (count($files) === 1)
        {
            // We renumber the remaining file/directory so that it is at index 0. It might not have been because of the subtraction of "." and ".."
            $files = array_values($files);
            $soleDirectory = Filesystem::combinePaths($destination, $files[0]);
            if (is_dir($soleDirectory))
            {
                if (Filesystem::copyIntoDirectory($soleDirectory, $destination))
                {
                    Filesystem::removeDir($soleDirectory);
                    return true;
                }
                else
                {
                    $errorMessage = "the ZIP file contained a single directory, but copying its contents to temporary directory failed";
                    return false;
                }
            }
        }
		return true;
	}
}

