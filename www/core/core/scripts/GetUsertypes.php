<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets all user types.
 * @n @b Requirements: one of following privileges: User::usersAdd, User::usersManage,
 *		User::usersPrivPresets
 * @n @b Arguments: none
 */
class GetUsertypes extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::usersAdd, User::usersManage, User::usersPrivPresets))
			return false;

		$userTypes = Repositories::getRepository(Repositories::UserType)->findAll();
		/**
		 * @var $userTypes \UserType[]
		 */
		foreach ($userTypes as $userType) {
			$this->addRowToOutput([
				$userType->getId(),
				$userType->getName(),
				User::instance()->unpackPrivileges($userType->getPrivileges())
			]);
		}
		return true;
	}
}

