<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Deletes problem (with assignments & submissions).
 * @n @b Requirements: either User::lecturesManageAll, or User::lecturesManageOwn and
 *		be the owner of the lecture this problem belongs to
 * @n @b Arguments:
 * @li @c id problem ID
 */
final class DeleteProblem extends DataScript
{
	protected function body ()
	{
		if (!$this->isInputValid(array('id' => 'isIndex')))
			return false;

		$id = $this->getParams('id');

		/**
		 * @var $problem \Problem
		 */
        $problem = Repositories::findEntity(Repositories::Problem, $id);
        $lecture = $problem->getLecture();
		$user = User::instance();
		if (!$user->hasPrivileges(User::lecturesManageAll) && (!$user->hasPrivileges(User::lecturesManageOwn)
				|| ($user->getId() != $lecture->getOwner())))
        {
            return $this->death(StringID::InsufficientPrivileges);
        }
        RemovalManager::hideProblemAndItsAssignments($problem);
		return true;
	}
}

