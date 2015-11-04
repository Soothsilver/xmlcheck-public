<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets all users.
 * @n @b Requirements: User::usersExplore privilege
 * @n @b Arguments: none
 */
class GetUsers extends DataScript
{
	protected function body ()
	{

		if (!$this->userHasPrivileges(User::usersExplore))
			return;

        /**
         * @var $users \User[]
         */
        $users = Repositories::getRepository(Repositories::User)->findAll();

        foreach ($users as $user)
        {
            if ($user->getDeleted() == true) { continue; }

            $this->addRowToOutput([
                $user->getId(),
                $user->getName(),
                $user->getType()->getId(),
                $user->getType()->getName(),
                $user->getRealName(),
                $user->getEmail(),
                $user->getLastAccess()->format("Y-m-d H:i:s")
            ]);
        }
	}
}

