<?php

namespace asm\core;
use asm\core\lang\StringID;

/**
 * @ingroup requests
 * Deletes assignment (with submissions).
 * @n @b Requirements: either User::groupsManageAll privilege, or User::groupsManageOwn
 *		and be the owner of the group this assignment belongs to
 * @n @b Arguments:
 * @li @c id assignment ID
 */
final class DeleteAssignment extends DataScript
{
	protected function body ()
	{
        if (!$this->isInputValid(array('id' => 'isIndex')))
        {
            return false;
        }
        /**
         * @var $assignment \Assignment
         */
        $assignment = Repositories::findEntity(Repositories::Assignment, $this->getParams('id'));
        $user = User::instance();
        if (!$user->hasPrivileges(User::groupsManageAll) &&
            (!$user->hasPrivileges(User::groupsManageOwn) || ($user->getId() != $assignment->getGroup()->getOwner()->getId())))
        {
            return $this->death(StringID::InsufficientPrivileges);
        }
        $assignment->setDeleted(true);
        Repositories::persistAndFlush($assignment);
        return true;
	}
}

