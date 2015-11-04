<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets assignments manageable by user.
 * @n @b Requirements: one of following privileges: User::groupsManageAll, User::groupsManageOwn
 * @n @b Arguments: none
 */
final class GetAssignments extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::groupsManageAll, User::groupsManageOwn))
			return false;

        /**
         * @var $assignments \Assignment[]
         */
        if (User::instance()->hasPrivileges(User::groupsManageAll)) {
            $assignments = Repositories::getRepository(Repositories::Assignment)->findBy(['deleted' => false]);
        }
        else {
            $assignments = Repositories::getEntityManager()->createQuery('SELECT a, g FROM \Assignment a JOIN a.group g WHERE a.deleted = false AND g.owner = :ownerId')->setParameter('ownerId', User::instance()->getId())->getResult();
        }
        foreach($assignments as $assignment)
        {
            $row = array(
                $assignment->getId(),
                $assignment->getProblem()->getId(),
                $assignment->getProblem()->getName(),
                $assignment->getDeadline()->format('Y-m-d H:i:s'),
                $assignment->getReward(),
                $assignment->getGroup()->getId(),
                $assignment->getGroup()->getName(),
                $assignment->getGroup()->getOwner()->getId()
            );
            $this->addRowToOutput($row);
        }
        return true;
	}
}

