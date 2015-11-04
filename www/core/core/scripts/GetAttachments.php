<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets all questions manageable by user.
 * @n @b Requirements: one of following privileges: User::lecturesManageAll,
 *		User::lecturesManageOwn
 * @n @b Arguments: none
 */
final class GetAttachments extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::lecturesManageAll, User::lecturesManageOwn))
			return false;


		$attachments = CommonQueries::GetAttachmentsVisibleToActiveUser();
		foreach ($attachments as $attachment) {
			$this->addRowToOutput([
				$attachment->getId(),
				$attachment->getName(),
				$attachment->getType(),
				$attachment->getLecture()->getId(),
				$attachment->getLecture()->getName(),
				$attachment->getLecture()->getDescription()
			]);
		}
		return true;
	}
}

