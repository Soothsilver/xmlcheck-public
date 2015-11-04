<?php

namespace asm\core;
use asm\utils\ShellUtils;

/**
 * @ref ILauncher "Launcher" of executable files.
 */
class ExeLauncher implements ILauncher
{
	public function launch ($file, array $arguments, &$output)
	{
		/// Supplied file path must point to an executable.

		if (!file_exists($file))
		{
			return 'File doesn\'t exist or cannot be read';
		}

		$file = realpath($file);
		$arguments = ShellUtils::makeShellArguments($arguments);
		$output = `"$file" $arguments`;

		return false;
	}
}

