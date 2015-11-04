<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * Implements common logic for handling of PermitSubscription and ProhibitSubscription requests.
 */
abstract class HandleSubscriptionRequest extends DataScript
{
	/**
	 * Sends appropriate database request updating the subscription.
	 * @param \Subscription $subscriptionRequest the subscription request to be confirmed or denied
	 */
	abstract protected function handleRequest (\Subscription $subscriptionRequest);

	protected function body ()
	{
		if (!$this->isInputSet('id'))
			return false;

		$id = $this->getParams('id');

		/**
		 * @var $subscription \Subscription
		 */
		$subscription = Repositories::findEntity(Repositories::Subscription, $id);
		if (User::instance()->getId() !== $subscription->getGroup()->getOwner()->getId())
		{
			return $this->death(StringID::InsufficientPrivileges);
		}

		$this->handleRequest($subscription);
		return true;
	}
}

