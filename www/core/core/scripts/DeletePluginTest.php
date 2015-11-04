<?php

namespace asm\core;

/**
 * @ingroup requests
 * Deletes plugin test.
 * @n @b Requirements: User::pluginsTest privilege
 * @n @b Arguments:
 * @li @c id plugin test ID
 */
final class DeletePluginTest extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::pluginsTest))
			return false;

		if (!$this->isInputValid(array('id' => 'isIndex')))
			return false;

		$id = $this->getParams('id');

		RemovalManager::deleteTestById($id);
		return true;
	}
}

