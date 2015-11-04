<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets groups user can subscribe to.
 * @n @b Requirements: must be logged in
 * @n @b Arguments: none
 */
final class GetAvailableGroups extends DataScript
{
	protected function body ()
    {
        if (!$this->userHasPrivileges(User::groupsJoinPublic, User::groupsJoinPrivate, User::groupsRequest))
        {
            return false;
        }

        $user = User::instance();
        $displayPublic = $user->hasPrivileges(User::groupsJoinPublic);
        $displayPrivate = $user->hasPrivileges(User::groupsJoinPrivate, User::groupsRequest);

        /**
         * @var $groups \Group[]
         * @var $subscriptions \Subscription[]
         */
        $subscriptions = Repositories::getRepository(Repositories::Subscription)->findBy(array('user' => $user->getId()));
        $subscriptionGroupIds = array_map(function ($subscription) { /** @var $subscription \Subscription */ return $subscription->getGroup()->getId(); }, $subscriptions);
        $conditions = array('deleted' => false);
        if (!$displayPrivate)
        {
            $conditions['type'] = 'public';
        }
        if (!$displayPublic)
        {
            $conditions['type'] = 'private';
        }
        $groups = Repositories::getRepository(Repositories::Group)->findBy($conditions);
        foreach ($groups as $group)
        {
            if (in_array($group->getId(), $subscriptionGroupIds))
            {
                continue;
            }
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

