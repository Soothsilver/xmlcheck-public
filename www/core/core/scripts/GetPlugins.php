<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets all plugins.
 * @n @b Requirements: one of following privileges: User::pluginsExplore,
 *		User::lecturesManageOwn, User::lecturesManageAll
 * @n @b Arguments: none
 */
final class GetPlugins extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::pluginsExplore, User::lecturesManageOwn, User::lecturesManageAll))
			return false;

		/** @var \Plugin[] $plugins */
		$plugins = Repositories::getRepository(Repositories::Plugin)->findAll();
		foreach ($plugins as $plugin) {
			$this->addRowToOutput([
				$plugin->getId(),
				$plugin->getName(),
				$plugin->getType(),
				$plugin->getDescription(),
				$plugin->getConfig()
			]);
		}
		return true;
	}
}

