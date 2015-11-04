<?php

namespace asm\core;

/**
 * @ingroup requests
 * Gets plugin test output file.
 * @n @b Requirements: User::pluginsTest privilege
 * @n @b Arguments:
 * @li @c id plugin test ID
 */
final class DownloadPluginTestOutput extends DownloadScript
{
	protected function body()
	{
		if (!$this->userHasPrivileges(User::pluginsTest))
			return false;
		if (!$this->isInputValid(array('id' => 'isIndex')))
			return false;
		/**
		 * @var $test \PluginTest
		 */
		$test = Repositories::findEntity(Repositories::PluginTest, $this->getParams('id'));
		$this->setOutput($test->getOutput(),
			Config::get('defaults', 'pluginOutputFileName') . '.zip');
		return true;
	}
}

