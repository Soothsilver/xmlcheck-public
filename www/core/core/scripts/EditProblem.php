<?php

namespace asm\core;

use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Creates or edits problem.
 * @n @b Requirements: either User::lecturesManageAll privilege, or User::lecturesManageOwn
 *		privilege and be the owner of the lecture this problem belongs to
 * @n @b Arguments:
 * @li @c id @optional problem ID (required for edit)
 * @li @c lecture @optional lecture ID (required for add, not editable)
 * @li @c name problem name (must match problem ID for edit)
 * @li @c description problem description
 * @li @c plugin plugin ID (zero for no plugin)
 */
final class EditProblem extends DataScript
{
	protected function body ()
	{
        // Verify input
		$inputs = array(
			'lecture' => 'isIndex',
			'name' => 'isNotEmpty',
			'description' => array(),
			'pluginId' => 'isIndex',
		);
		if (!$this->isInputValid($inputs))
			return false;

        // Load data from user and database
        $lectureIndex = $this->getParams('lecture');
        /**
         * @var $lecture \Lecture
         * @var $problem \Problem
         * @var $plugin \Plugin
         */
        $lecture = Repositories::findEntity(Repositories::Lecture, $lectureIndex);
        $name = $this->getParams('name');
        $description = $this->getParams('description');
        $pluginIdOrZero = $this->getParams('pluginId');
        $plugin = null;
        if ($pluginIdOrZero !== 0)
        {
            $plugin = Repositories::findEntity(Repositories::Plugin, $pluginIdOrZero);
        }
        $pluginArguments = $this->getParams('pluginArguments');
        $pluginArguments = ($pluginArguments !== null) ? $pluginArguments : '';
        $problemId = $this->getParams('id');
        $isIdSet = ($problemId !== null) && ($problemId !== '');

        // Verify privileges
        $user = User::instance();
        if (!$user->hasPrivileges(User::lecturesManageAll)
            && (!$user->hasPrivileges(User::lecturesManageOwn)
                || ($lecture->getOwner() != $user->getId()))) {
            return $this->death(StringID::InsufficientPrivileges);
        }

        if ($isIdSet) {
            $problem = Repositories::findEntity(Repositories::Problem, $problemId);
            $problem->setDescription($description);
            $problem->setPlugin($plugin);
            $problem->setConfig($pluginArguments);
            Repositories::persistAndFlush($problem);
        }
        else {
            $problem = new \Problem();
            $problem->setLecture($lecture);
            $problem->setName($name);
            $problem->setDescription($description);
            $problem->setPlugin($plugin);
            $problem->setConfig($pluginArguments);
            Repositories::persistAndFlush($problem);
        }
        return true;
	}
}

