<?php

namespace asm\core;


/**
 * @ingroup requests
 * Creates or updates user type.
 * @n @b Requirements: User::usersPrivPresets privilege
 * @n @b Arguments:
 * @li @c id @optional user type ID (required for edit)
 * @li @c name user type name (must match user type ID for edit)
 * @li @c users names of privileges separated by semicolons (possible names:
 *		@c usersAdd, @c usersManage, @c usersRemove, @c usersExplore, @c usersPrivPresets)
 * @li @c subscriptions --||-- (@c groupsJoinPublic, @c groupsRequest, @c groupsJoinPrivate)
 * @li @c plugins --||-- (@c pluginsAdd, @c pluginsManage, @c pluginsTest, @c pluginsExplore,
 *		@c pluginsRemove)
 * @li @c assignments --||-- (@c assignmentsSubmit)
 * @li @c submissions --||-- (@c submissionsCorrect)
 * @li @c lectures --||-- (@c lecturesAdd, @c lecturesManageAll, @c lecturesManageOwn)
 * @li @c groups --||-- (@c groupsAdd, @c groupsManageAll, @c groupsManageOwn)
 */
final class EditUsertype extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::usersPrivPresets))
			return false;

		$privilegeGroups = array('users', 'subscriptions', 'plugins', 'assignments', 'submissions', 'lectures', 'groups', 'other');
		$inputs = array_merge(array(
			'name' => array(
				'isAlphaNumeric',
				'isNotEmpty'
			),
		), array_combine($privilegeGroups, array_pad(array(), count($privilegeGroups), array())));
		if (!$this->isInputValid($inputs))
			return false;

		$id = $this->getParams('id');
		$name = $this->getParams('name');
		
		$privileges = array();
		foreach ($privilegeGroups as $i => $group)
		{
			$value = $this->getParams($group);
			$privileges = array_merge($privileges, (($value != '') ? explode(';', $value) : array()));
		}
		if (count($privileges))
		{
			$privileges = array_combine($privileges, array_pad(array(), count($privileges), true));
		}
		$privileges = User::instance()->packPrivileges($privileges);

		if (($id === null) || ($id === ''))
		{
			$usertype = new \UserType();
			$usertype->setName($name);
			$usertype->setPrivileges($privileges);
			Repositories::persistAndFlush($usertype);
		}
		else
		{
			/** @var \UserType $usertype */
			$usertype = Repositories::findEntity(Repositories::UserType, $id);
			$usertype->setName($name);
			$usertype->setPrivileges($privileges);
			Repositories::persistAndFlush($usertype);
		}
		return true;
	}
}

