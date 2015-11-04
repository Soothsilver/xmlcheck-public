<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Deletes user.
 * @n @b Requirements: User::usersRemove privilege
 * @n @b Arguments:
 * @li @c id user ID
 */
class DeleteUser extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::usersRemove))
			return false;

		if (!$this->isInputSet('id'))
			return false;

		$id = $this->getParams('id');
        if ($id == User::instance()->getId())
        {
            return $this->death(StringID::YouCannotRemoveYourself);
        }

		/** @var \User $user */
        $user = Repositories::findEntity(Repositories::User, $id);
        RemovalManager::hideUserAndAllHeOwns($user);
		return true;
	}
}

