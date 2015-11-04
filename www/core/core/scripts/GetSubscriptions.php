<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets user's subscriptions.
 * @n @b Requirements: one of following privileges: User::groupsJoinPublic,
 *		User::groupsJoinPrivate, User::groupsRequest
 * @n @b Arguments: none
 */
final class GetSubscriptions extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::groupsJoinPublic, User::groupsJoinPrivate, User::groupsRequest))
			return false;
		$subscriptions = Repositories::getRepository(Repositories::Subscription)->findBy(['user' => User::instance()->getId()]);
		foreach($subscriptions as $subscription) {
			/**
			 * @var $subscription \Subscription
			 */
			$this->addRowToOutput([
				$subscription->getId(),
				$subscription->getGroup()->getName(),
				$subscription->getGroup()->getDescription(),
				$subscription->getGroup()->getLecture()->getName(),
				$subscription->getGroup()->getLecture()->getDescription(),
				$subscription->getStatus()
			]);
		}
		return true;
	}
}

