<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets all plugin tests.
 * @n @b Requirements: User::pluginsTest privilege
 * @n @b Arguments: none
 */
final class GetPluginTests extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::pluginsTest))
			return false;

		/**
		 * @var $tests \PluginTest[]
		 */
		$tests = Repositories::getRepository(Repositories::PluginTest)->findAll();
		foreach($tests as $test) {
			$this->addRowToOutput([
				$test->getId(),
				$test->getDescription(),
				$test->getPlugin()->getName(),
				$test->getPlugin()->getDescription(),
				$test->getPlugin()->getConfig(),
				$test->getConfig(),
				$test->getStatus(),
				$test->getSuccess(),
				$test->getInfo(),
				($test->getOutput() ? "yes" : "")
			]);
		}
		return true;
	}
}

