<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Creates or edits group.
 * @n @b Requirements: User::groupsAdd privilege for add and either User::groupsManageAll
 *		privilege or User::groupsManageOwn privilege and be the group's owner for edit
 * @n @b Arguments:
 * @li @c id @optional group ID (required for edit)
 * @li @c lecture @optional lecture ID (required for add, not editable)
 * @li @c name group name (not editable, must match group ID for edit)
 * @li @c description group description
 */
final class EditGroup extends DataScript
{
	protected function body ()
    {
        $inputs = array(
            'lecture' => 'isIndex',
            'name' => 'isNotEmpty',
            'description' => array()
        );
        if (!$this->isInputValid($inputs))
        {
            return false;
        }
        $lectureIndex = $this->getParams('lecture');
        $groupName = $this->getParams('name');
        $groupDescription = $this->getParams('description');
        $public = $this->paramExists('public') ? 'public' : 'private';
        $groupId = $this->getParams('id');
        $editing = $groupId !== null && $groupId !== '';
        $user = User::instance();
        /** @var \Lecture $lecture */
        $lecture = Repositories::findEntity(Repositories::Lecture, $lectureIndex);

        if ($editing)
        {
            /**
             * @var $group \Group
             */
            $group = Repositories::findEntity(Repositories::Group, $groupId);
            $group->setName($groupName);
            $group->setDescription($groupDescription);
            $group->setType($public);
            Repositories::persistAndFlush($group);
        }
        else
        {
            if (!$this->userHasPrivileges(User::groupsAdd)) { return $this->death(StringID::InsufficientPrivileges); }

            $group = new \Group();
            $group->setDeleted(false);
            $group->setDescription($groupDescription);
            $group->setLecture($lecture);
            $group->setName($groupName);
            $group->setOwner($user->getEntity());
            $group->setType($public);
            Repositories::persistAndFlush($group);
        }
        return true;

	}
}

