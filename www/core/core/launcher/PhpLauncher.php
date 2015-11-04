<?php

namespace asm\core;


/**
 * @ref ILauncher "Launcher" of php scripts.
 */
class PhpLauncher implements ILauncher
{
	public function launch ($file, array $arguments, &$output)
	{
		/// Supplied file path must point to PHP file "\<CLASS\>.php", where \<CLASS\>
		/// is name of contained main class (must have public run() method).

		if (!file_exists($file))
		{
			return 'File doesn\'t exist or cannot be read';
		}

		/** @noinspection PhpIncludeInspection */
		require_once $file;
		$mainClass = basename($file, '.php');
		if (!class_exists($mainClass, false))
		{
			return 'Main class ' . $mainClass . ' doesn\'t exist or is improperly named';
		}


		/**
		 * @var $script \asm\plugin\Plugin
		 */
		$script = new $mainClass();
		if (!is_callable(array($script, 'run')))
		{
			return 'Main class must have callable run() method.';
		}

		$output = $script->run($arguments);
		return false;
	}
}

