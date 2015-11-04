<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets subscription requests to user's groups.
 * @n @b Requirements: User::groupsAdd privilege
 * @n @b Arguments: none
 */
final class GetSubscriptionRequests extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::groupsAdd))
			return false;

		$requests = Repositories::getEntityManager()->createQuery("SELECT s, g FROM \Subscription s JOIN s.group g WHERE g.owner = :ownerId")
			->setParameter("ownerId", User::instance()->getId())
			->getResult();
		foreach ($requests as $request) {
			/**
			 * @var $request \Subscription
			 */
			if ($request->getStatus() !== \Subscription::STATUS_REQUESTED) { continue; }
			$this->addRowToOutput([
				$request->getId(),
				$request->getUser()->getName(),
				$request->getUser()->getRealName(),
				$request->getUser()->getEmail(),
				$request->getGroup()->getName(),
				$request->getGroup()->getDescription(),
				$request->getGroup()->getLecture()->getName(),
				$request->getGroup()->getLecture()->getDescription()
			]);
		}
		return true;
	}
}

