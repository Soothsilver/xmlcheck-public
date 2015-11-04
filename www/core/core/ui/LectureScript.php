<?php

namespace asm\core;


/**
 * This class contains methods used in scripts that need to verify printable-test-related privileges.
 */
abstract class LectureScript extends DataScript
{
	/**
	 * Returns true if the active user is authorized to manage the lecture specified by the parameter.
	 * @param $lectureId int Database ID of the lecture.
	 * @return bool Is the active user authorized to manage it?
     */
	protected function checkTestGenerationPrivileges ($lectureId)
	{
		/**
		 * @var $lecture \Lecture
		 */
		$lecture = Repositories::findEntity(Repositories::Lecture, $lectureId);
		return $this->authorizedToManageLecture($lecture);
	}
}

