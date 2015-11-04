<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets all questions manageable by user.
 * @n @b Requirements: one of following privileges: User::lecturesManageAll,
 *		User::lecturesManageOwn
 * @n @b Arguments: none
 */
final class GetQuestions extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::lecturesManageAll, User::lecturesManageOwn))
			return false;

		$questions = CommonQueries::GetQuestionsVisibleToActiveUser();
		foreach($questions as $question){
			$this->addRowToOutput([
				$question->getId(),
				$question->getText(),
				$question->getType(),
				$question->getOptions(),
				$question->getAttachments(),
				$question->getLecture()->getId(),
				$question->getLecture()->getName(),
				$question->getLecture()->getDescription()
			]);
		}
		return true;
	}
}

