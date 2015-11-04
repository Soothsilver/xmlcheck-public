<?php

namespace asm\core;

/**
 * @ingroup requests
 * Permits some other user's subscription request to group owned by this user.
 * @n @b Requirements: user has to own the group the subscription request is for
 * @n @b Arguments:
 * @li @c id group id
 */
final class PermitSubscription extends HandleSubscriptionRequest
{
	protected function handleRequest (\Subscription $subscriptionRequest)
	{
		$subscriptionRequest->setStatus(\Subscription::STATUS_SUBSCRIBED);
		Repositories::persistAndFlush($subscriptionRequest);
	}
}

