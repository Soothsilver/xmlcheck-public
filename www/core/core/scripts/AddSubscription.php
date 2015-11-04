<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Add subscription for current user.
 * @n @b Requirements: one of following privileges: User::groupsJoinPrivate,
 *		User::groupsJoinPublic, or User::groupsRequest
 * @n @b Arguments:
 * @li @c id group ID
 */
class AddSubscription extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::groupsJoinPrivate, User::groupsJoinPublic, User::groupsRequest))
			return false;

		if (!$this->isInputValid(array('id' => 'isIndex')))
			return false;

		$groupId = $this->getParams('id');
		/**
		 * @var $group \Group
		 */
		$group = Repositories::findEntity(Repositories::Group, $groupId);

		// Calculate privileges of the user
		$user = User::instance();
		$canJoinPrivate = User::instance()->hasPrivileges(User::groupsJoinPrivate);
		$groupIsPrivate = $group->getType() == \Group::TYPE_PRIVATE;
		$hasSufficientPrivileges =
			($groupIsPrivate
				&&
				($canJoinPrivate || $user->hasPrivileges(User::groupsRequest)))
				 // Joining or requesting to be inside a private group
		 ||
			(!$groupIsPrivate && $user->hasPrivileges(User::groupsJoinPublic))
			// Joining a public group
		;
		if (!$hasSufficientPrivileges)
		{
			return $this->death(StringID::InsufficientPrivileges);
		}
		$status = ($canJoinPrivate || !$groupIsPrivate) ? \Subscription::STATUS_SUBSCRIBED : \Subscription::STATUS_REQUESTED;

		// Put into database
		$subscription = new \Subscription();
		$subscription->setGroup($group);
		$subscription->setUser(User::instance()->getEntity());
		$subscription->setStatus($status);
		Repositories::persistAndFlush($subscription);
		return true;
	}
}

