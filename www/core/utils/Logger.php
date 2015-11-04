<?php

namespace asm\utils;
use DateTime;

/**
 * Manages error log.
 *
 * Log consists of entries, each of which should contain error information about
 * a single request that went wrong. Each entry has a single header and any number
 * of 'lines' (error descriptions). Header contains timestamp and possibly other
 * customizable items. Lines are fully customizable. Header and line item sets
 * should be consistent throughout the whole log.
 *
 * Usual use of Logger is to create single instance for the whole script execution
 * and use it to log errors. At the end of script execution all errors are
 * automatically packed into single log entry and added to log file.
 *
 * Log data is saved in log files. Maximum size of individual files and their
 * number can be customized. Log is 'rotating' - when the file is full, it
 * continues writing into next one (when the last one is full, it continues with
 * the first one, etc.).
 *
 * The rotation works like this: Suppose the maximum file count is 5. When the fifth is written,
 * there will be files log0, log1, log2, log3, log4. When a log (in this case log4) is full,
 * we find the first log that does not exist in order (log5) and create it. We will delete the oldest,
 * log0, so we now have log1, log2, ... log5. When log5 becomes full, the first log that does not exist
 * is log0, so we create that and delete log1, and we now have log0, log2, log3, log4, log5.
 *
 * By the position of this "break" in file names, we keep track of which file is the oldest and newest.
 *
 * The Logger flushes (writes to disk) when it is destroyed.
 */
class Logger
{
	/**
	 * Creates new Logger instance.
	 * @param string $folder folder in which the log files are (to be) located
	 * @return Logger new instance
	 */
	public static function create ($folder)
	{
		return new self($folder);
	}

	/**
	 * @var string Folder to store log files in
     */
	private $folder;
	/**
	 * @var array Lines in a log entry (a line should contain an error description)
     */
	private $lines = [];
	/**
	 * @var string Header written before each log entry
     */
	private $header = 'Log Entry';
	/**
	 * @var string Prefix for the logfile filename
     */
	private $prefix = 'logfile';
	/**
	 * @var string Suffix for the logfile filename
     */
	private $suffix = '.log';
	/**
	 * @var int Maximum size of a logfile in byes
     */
	private $maxFileSize = 1048576;
	/**
	 * @var int Maximum number of log files. When this is exceeded, the old logfiles start to be overwritten.
     */
	private $maxFileCount = 5;
	/**
	 * @var string Separator present between two log entries
     */
	private $entrySeparator = "\n#ENTRY\n";
	/**
	 * @var string Separator present between two log lines
     */
	private $lineSeparator = "\n#LINE\n";
	/**
	 * @var string The format (as for the DateTime::toString() function) to print the date with
     */
	private $datetimeFormat = DateTime::ISO8601;

	/**
	 * (Creates and) sets folder for log files to be saved in.
	 * @param string $folder logfile folder
	 */
	private function __construct ($folder)
	{
		Filesystem::createDir($folder, 0700);
		$this->folder = realpath($folder);
	}

	/**
	 * Writes log entry to log file if it's not empty.
	 */
	function  __destruct ()
	{

        // Writing into a log file is not very important.
        // The silence operator will ensure the user gets the results even if logging fails.
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        @$this->flush();
	}

	/**
	 * Sets prefix of log file names (logfiles are named \<PREFIX\>\<INDEX\>\<SUFFIX\>).
	 * @param string $prefix
	 * @return Logger self
	 */
	public final function setPrefix ($prefix)
	{
		$this->prefix = basename($prefix);
		return $this;
	}

	/**
	 * Sets suffix of log file names (logfiles are named \<PREFIX\>\<INDEX\>\<SUFFIX\>).
	 * @param string $suffix
	 * @return Logger self
	 */
	public final function setSuffix ($suffix)
	{
		$this->suffix = basename($suffix);
		return $this;
	}

	/**
	 * Sets maximum size of log files.
	 * @param int $size maximum file size in bytes
	 * @return Logger self
	 */
	public final function setMaxFileSize ($size)
	{
		$this->maxFileSize = (int)$size;
		return $this;
	}

	/**
	 * Sets maximum number of log files (log rotation starts when this number is reached).
	 * @param int $count
	 * @return Logger self
	 */
	public final function setMaxFileCount ($count)
	{
		$this->maxFileCount = (int)$count;
		return $this;
	}

	/**
	 * Sets entry separator.
	 * @param string $separator
	 * @return Logger self
	 */
	public final function setEntrySeparator ($separator)
	{
		$this->entrySeparator = (string)$separator;
		return $this;
	}

	/**
	 * Sets line separator.
	 * @param string $separator
	 * @return Logger self
	 */
	public final function setLineSeparator ($separator)
	{
		$this->lineSeparator = (string)$separator;
		return $this;
	}


	/**
	 * Sets timestamp format.
	 * @param string $format Look up PHP @c date function for formatting options.
	 *		Separators will be stripped from value.
	 * @return Logger self
	 */
	public final function setDatetimeFormat ($format)
	{
		$this->datetimeFormat = $format;
		return $this;
	}

	/**
	 * Sets items to be saved in log entry header apart from timestamp.
	 * @param string $headerText the title of the next log entry to be written
	 * @return Logger self
	 */
	public final function setHeader ($headerText)
	{
		$this->header = $headerText;
		return $this;
	}

	/**
	 * Log error (or anything really).
	 * @param string $line line to be saved to log
	 * @return Logger self
	 */
	public function log ($line)
	{
        $this->lines[] = $line;
		return $this;
	}

	/**
	 * Creates full path to log file with supplied index.
	 * @param int $index
	 * @return string log file path
	 */
	private function getLogFilename ($index)
	{
		return $this->folder . DIRECTORY_SEPARATOR . $this->prefix . $index . $this->suffix;
	}

	/**
	 * Create index of currently existing log files with info about current log file index.
     * The first element of the array is the oldest file.
	 * @return array combined array with logfile names as simple entries and an
	 *		additional entry 'lastIndex' indicating index of most recent logfile
	 */
	private function getLogFiles ()
	{
		$lastIndex = null;
		$lastFoundIndex = 0;
		$reachedBreak = false;
		$beforeBreak = [];
		$afterBreak = [];
		for ($i = 0; $i <= $this->maxFileCount; ++$i)
		{
			$filename = $this->getLogFilename($i);
			if (!is_file($filename))
			{
				$reachedBreak = true;
				continue;
			}

			if ($reachedBreak)
			{
				$afterBreak[] = $filename;
				$lastFoundIndex = $i;
			}
			else
			{
				$beforeBreak[] = $filename;
				$lastIndex = $i;
			}
		}

		$logFiles = array_merge($afterBreak, $beforeBreak);

		if (($lastIndex === null) && count($logFiles))
		{
			$lastIndex = $lastFoundIndex;
		}
		
		$logFiles['lastIndex'] = $lastIndex;

		return $logFiles;
	}

	/**
	 * Gets logfile index coming after supplied index (indexes are rotated).
	 * @param int $index
	 * @return int next index
	 */
	private function getNextIndex ($index)
	{
		return ($index + 1) % ($this->maxFileCount + 1);
	}

	/**
	 * Turns log entry data stored in this Logger instance to string to be written
	 * to logfile.
	 * @return string escaped and separated log entry data
	 */
	private function entryToString ()
	{
		$headerLine = date($this->datetimeFormat) . " " . $this->header;

		$lines = array_merge( [$headerLine], $this->lines);

		$entry = $this->entrySeparator . implode($this->lineSeparator, $lines);

		if (strlen($entry) > $this->maxFileSize)
		{
			$entry = substr($entry, 0, $this->maxFileSize);
		}

		return $entry;
	}

	/**
	 * Manages writing of stored data to logfile (and possibly rotating log).
	 *
	 * Entry is written only if not empty. If most recent logfile is full, oldest
	 * logfile is deleted and new file is created to contain the entry. Locking
	 * is used to prevent logfile access clashes.
	 * @return Logger self
	 */
    private function write ()
	{
		if (!count($this->lines))
		{
			return $this;
		}

		$entryString = $this->entryToString();

		$logFiles = $this->getLogFiles();
		$lastIndex = $logFiles['lastIndex'];
		unset($logFiles['lastIndex']);
		$noFile = ($lastIndex === null);

		$currentFileSize = $noFile	? 0 : filesize($this->getLogFilename($lastIndex));
		$currentIndex = $noFile	? 0 : $lastIndex;

		$deleteOldest = (($currentFileSize + strlen($entryString)) > $this->maxFileSize);
		if ($deleteOldest)
		{
			$currentIndex = $this->getNextIndex($currentIndex);
		}

		$currentFile = fopen($this->getLogFilename($currentIndex), 'a');
		flock($currentFile, LOCK_EX);

		if ($deleteOldest)
		{
			if ((count($logFiles) >= $this->maxFileCount) && file_exists($logFiles[0]))
			{
				chmod($logFiles[0], 0777);
				unlink($logFiles[0]);
			}
		}

		fwrite($currentFile, $entryString);
		flock($currentFile, LOCK_UN);
		fclose($currentFile);

		clearstatcache();
		
		return $this;
	}

	/**
	 * Clears all events logged so far during this script execution.
	 * @return Logger self
	 */
    private function clear ()
	{
		$this->lines = [];
		return $this;
	}

	/**
	 * Writes logged events to logfile and remove them from this instance (to avoid
	 * saving them more than once).
     *
     * Will only write if there is at least one line to write.
	 * @return Logger self
	 */
	public final function flush ()
	{
		return $this->write()->clear();
	}

}

