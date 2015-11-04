<?php

namespace asm\core;
use asm\core\lang\StringID;
use asm\utils\Filesystem;

/**
 * @ingroup requests
 * Deletes plugin (with problems, assignments, and submissions).
 *
 *  This is a very destructive operation because all problems associated with this plugin will lose any reference to it and thus the submissions will lose reference and therefore we won't be able to use them for sooth.similarity comparison, for example.
 * @n @b Requirements: User::pluginsRemove privilege
 * @n @b Arguments: 
 * @li @c id plugin ID
 */
final class DeletePlugin extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::pluginsRemove))
			return false;

		if (!$this->isInputValid(array('id' => 'isIndex')))
			return false;

		$id = $this->getParams('id');
		/**
		 * @var $plugin \Plugin
		 */
		$plugin = Repositories::findEntity(Repositories::Plugin, $id);

		$pluginFolder = Filesystem::combinePaths(Config::get('paths', 'plugins'), $plugin->getName());
		if (!Filesystem::removeDir($pluginFolder))
			return $this->death(StringID::FileSystemError);

		RemovalManager::deletePluginById($id);
		return true;
	}
}

