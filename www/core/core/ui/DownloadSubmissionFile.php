<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * Contains convenience method for getting submission by ID if it's accessible to user.
 *
 * @see UiScript for explanation of [stopping] tag.
 */
abstract class DownloadSubmissionFile extends DownloadScript
{
	/**
	 * Gets submission with supplied ID if it's accessible to user [stopping].
	 * @param int $id submission ID
	 * @return \Submission submission or false in case of failure
	 */
	protected final function findAccessibleSubmissionById ($id)
	{
		/**
		 * @var $submission \Submission
		 */
		$submission = Repositories::findEntity(Repositories::Submission, $id);
		$userId = User::instance()->getId();
		$authorId = $submission->getUser()->getId();
		$ownerId = $submission->getAssignment()->getGroup()->getOwner()->getId();

		if ($authorId !== $userId && $ownerId !== $userId)
		{
			if (User::instance()->hasPrivileges(User::groupsManageAll, User::lecturesManageAll, User::otherAdministration))
			{
				return $submission;
			}
			return $this->death(StringID::InsufficientPrivileges);
		}
		return $submission;
	}
}

