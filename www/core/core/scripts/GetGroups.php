<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets groups manageable by user.
 * @n @b Requirements: one of following privileges: User::groupsAdd, User::groupsManageAll,
 *		User::groupsManageOwn
 * @n @b Arguments: none
 */
final class GetGroups extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::groupsAdd, User::groupsManageAll, User::groupsManageOwn))
			return false;

		$user = User::instance();
		$displayAll = $user->hasPrivileges(User::groupsManageAll);

        $groups = ( $displayAll ? Repositories::getRepository(Repositories::Group)->findBy(array('deleted' => false))
            : Repositories::getRepository(Repositories::Group)->findBy(array('deleted' => false, 'owner' => $user->getId())) );

        /**
         * @var $group \Group
         */
        foreach($groups as $group)
        {
            $row = array(
                $group->getId(),
                $group->getName(),
                $group->getDescription(),
                $group->getType(),
                $group->getLecture()->getId(),
                $group->getLecture()->getName(),
                $group->getLecture()->getDescription()
            );
            $this->addRowToOutput($row);
        }
        return true;
	}
}
