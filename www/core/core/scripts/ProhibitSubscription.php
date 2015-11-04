<?php

namespace asm\core;

/**
 * @ingroup requests
 * Denies some other user's subscription request to group owned by this user.
 * @n @b Requirements: user has to own the group the subscription request is for
 * @n @b Arguments: 
 * @li @c id group id
 */
final class ProhibitSubscription extends HandleSubscriptionRequest
{
	protected function handleRequest (\Subscription $subscriptionRequest)
	{
		Repositories::remove($subscriptionRequest);
	}
}

