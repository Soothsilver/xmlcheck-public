<?php

namespace asm\core;
use asm\core\lang\StringID;

/**
 * @ingroup requests
 * Deletes group (with subscriptions, assignments, submissions).
 * @n @b Requirements: either User::groupsManageAll, or User::groupsManageOwn and be
 *		the group's owner (creator)
 * @n @b Arguments:
 * @li @c id group ID
 */
final class DeleteGroup extends DataScript
{
	protected function body ()
	{
        if (!$this->isInputValid(array('id' => 'isIndex')))
        {
            return false;
        }
        /**
         * @var $group \Group
         */
        $group = Repositories::findEntity(Repositories::Group, $this->getParams('id'));
        $user = User::instance();
        if (!$user->hasPrivileges(User::groupsManageAll) &&
            (!$user->hasPrivileges(User::groupsManageOwn) || ($user->getId() != $group->getOwner()->getId())))
        {
            return $this->death(StringID::InsufficientPrivileges);
        }
        RemovalManager::hideGroupAndItsAssignments($group);
        return true;
	}
}

