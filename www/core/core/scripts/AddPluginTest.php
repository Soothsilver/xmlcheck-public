<?php

namespace asm\core;
use asm\utils\StringUtils;

/**
 * @ingroup requests
 * Creates and runs new plugin test.
 * @n @b Requirements: User::pluginsTest privilege
 * @n @b Arguments:
 * @li @c description plugin test description
 * @li @c plugin plugin ID
 * @li @c config plugin configuration used for this test
 */
final class AddPluginTest extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::pluginsTest))
			return false;

		$inputs = array(
			'description' => 'isNotEmpty',
			'plugin' => 'isIndex',
			'config' => null,
		);

		if (!$this->isInputValid($inputs))
			return false;

		$description = $this->getParams('description');
		$pluginId = $this->getParams('plugin');
		/**
		 * @var $plugin \Plugin
		 */
		$plugin = Repositories::findEntity(Repositories::Plugin, $pluginId);
		$config = $this->getParams('config');

		$testFolder = Config::get('paths', 'tests');
		$inputFile = date('Y-m-d_H-i-s_') . StringUtils::randomString(10) . '.zip';

		if (!$this->saveUploadedFile('input', $testFolder . $inputFile))
			return false;

		$test = new \PluginTest();
		$test->setConfig($config);
		$test->setDescription($description);
		$test->setInput($inputFile);
		$test->setPlugin($plugin);
		Repositories::persistAndFlush($test);
		$pluginArguments = empty($test->getConfig()) ? [] : explode(';', $test->getConfig());
		return Core::launchPlugin($test->getPlugin()->getType(),
			Config::get('paths', 'plugins') . $test->getPlugin()->getMainfile(),
			$testFolder . $test->getInput(),
			true,
			$test->getId(),
			$pluginArguments);
	}
}

