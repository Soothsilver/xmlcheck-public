<?php

namespace asm\core;

/**
 * Launcher classes are used to launch separate programs/scripts.
 */
interface ILauncher
{
	/**
	 * Launches separate program/script file with supplied arguments.
	 * @param string $file path to script file
	 * @param array $arguments simple array with arguments (keys are unused)
	 * @param[out] string $output script output is saved to this variable
	 * @return mixed error message (string) or false in case of success
	 */
	public function launch ($file, array $arguments, &$output);
}

