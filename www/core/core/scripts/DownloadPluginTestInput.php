<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets plugin test input file.
 * @n @b Requirements: User::pluginsTest privilege
 * @n @b Arguments:
 * @li @c id plugin test ID
 */
final class DownloadPluginTestInput extends DownloadScript
{	protected function body()
	{
		if (!$this->userHasPrivileges(User::pluginsTest))
			return false;
		if (!$this->isInputValid(array('id' => 'isIndex')))
			return false;
		/**
		 * @var $test \PluginTest
		 */
		$test = Repositories::findEntity(Repositories::PluginTest, $this->getParams('id'));
		$this->setOutput(Config::get('paths', 'tests') . $test->getInput(),
			Config::get('defaults', 'pluginTestFileName') . '.zip');
		return true;
	}
}

