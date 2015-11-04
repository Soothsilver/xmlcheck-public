<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Deletes subscription.
 * @n @b Requirements: must be owner of the subscription
 * @n @b Arguments:
 * @li @c id subscription ID
 */
final class DeleteSubscription extends DataScript
{
	protected function body ()
	{
		if (!$this->isInputValid(array('id' => 'isIndex')))
			return false;

		$id = $this->getParams('id');
		/**
		 * @var $subscription \Subscription
		 */
		$subscription = Repositories::findEntity(Repositories::Subscription, $id);
		if ($subscription->getUser()->getId() !== User::instance()->getId())
		{
			return $this->death(StringID::InsufficientPrivileges);
		}
		Repositories::remove($subscription);
		return true;
	}
}

