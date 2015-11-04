<?php

namespace asm\utils;
use InvalidArgumentException;

/**
 * Shell-oriented utility functions.
 */
class ShellUtils
{
	/**
	 * Execute php code in a separate detached process. End-of-line characters in the PHP code are replaced by spaces.
     *
	 * @param string $phpCli path to PHP command-line interface
	 * @param string $phpCode php code to be executed
	 * @param string $dir base folder for code execution, or null for current directory
	 */
	public static function phpExecInBackground ($phpCli, $phpCode, $dir = null)
	{
		$code = str_replace("\r", ' ', str_replace("\n", ' ', $phpCode));
        // https://bugs.php.net/bug.php?id=68140
		$command = '"' . $phpCli . '" -r ' . self::escapeShellArgument($code);
		self::shellExecInBackground($command, $dir);
	}

	/**
	 * Execute shell command in a separate detached process. Works both on Windows and Linux.
     *
	 * @param string $command shell command (must be correctly escaped, etc.)
	 * @param string $dir folder to execute command in
	 */
	public static function shellExecInBackground ($command, $dir = null)
	{
		$dir = (($dir != null) && (is_dir($dir))) ? realpath($dir) : getcwd();
		$cmd = (substr(php_uname(), 0, 7) == "Windows")
				? 'start /b "Assignment Manager Detached Evaluation" ' . $command . ' && exit'
				: $command . ' > /dev/null &';

		proc_close(proc_open($cmd, [], $dummy, $dir));
	}

	/**
	 * Turn array of arguments into shell argument string.
     *
	 * @param array $arguments simple array of arguments
	 * @return string arguments escaped and separated by spaces
	 */
	public static function makeShellArguments (array $arguments)
	{
        $newArray = [];
        for($i =0; $i< count($arguments); $i++)
        {
            $newArray[] = self::escapeShellArgument($arguments[$i]);
        }
		return implode(' ', $newArray);
	}

	/**
	 * Turn value to string that can be inserted in PHP code as function argument.
     *
	 * @param mixed $arg int, float, string, array, or null
	 * @return string PHP function argument string
	 * @throws InvalidArgumentException in case @a $arg is not correctly typed
	 */
	public static function quotePhpArgument ($arg)
	{

		if (is_bool($arg))
		{
			return $arg ? 'true' : 'false';
		}
		elseif (is_int($arg) || is_float($arg))
		{
			return (string)$arg;
		}
		elseif (is_string($arg))
		{
			return "'" . addslashes($arg) . "'";
		}
		elseif (is_array($arg))
		{
			$quoted = [];
			foreach ($arg as $key => $value)
			{
				$quoted[] = self::quotePhpArgument($key) . ' => '
						. self::quotePhpArgument($value);
			}
			return 'array(' . implode(', ', $quoted) . ')';
		}
		elseif ($arg === null)
		{
			return 'null';
		}
		else
		{
			throw new InvalidArgumentException(
					"Cannot quote argument (must be a number, string, array, or null)");
		}
	}

	/**
	 * Turn array into string that can be inserted in PHP code as function argument set.
     *
	 * @param array $arguments each argument must be int, float, string, array, or null
	 * @return string PHP function argument set string
	 */
	public static function quotePhpArguments (array $arguments)
	{
		$quoted = [];
		foreach ($arguments as $argument)
		{
			$quoted[] = self::quotePhpArgument($argument);
		}
		return implode(', ', $quoted);
	}

    /**
     * Escapes an argument as though it were passed to escapeshellarg, except this works also on Windows.
     *
     * @param $argument string
     * @return string
     */
    public static function escapeShellArgument($argument)
    {
        return ((substr(php_uname(), 0, 7) == "Windows") ? '"' . addcslashes($argument, '\\"') . '"' : escapeshellarg($argument));
    }
}

