<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Creates a test template.
 * @n @b Requirements: either User::lecturesManageAll privilege, or User::lecturesManageOwn
 *		privilege and be the owner of the lecture this test belongs to
 * @n @b Arguments:
 * @li @c description test description
 * @li @c count number of questions the test is to have
 * @li @c questions question IDs in a single string consisting of two parts separated
 *		by semicolon. First part are mandatory questions, second part are the optional
 *		questions. IDs are separated by commas.
 */
final class AddTest extends GenTestScript
{
	protected function body ()
	{
		$questions = $this->getParams('questions');
		if ($questions === null || $questions === '')
		{
			return $this->death(StringID::ChooseAtLeastOneQuestion);
			// Put this in front to have a more specific, comprehensible error message
		}

		$inputs = array(
			'description' => 'isNotEmpty',
			'count' => 'isNonNegativeInt',
			'questions' => 'isNotEmpty',
		);
		if (!$this->isInputValid($inputs))
			return false;
		$description = $this->getParams('description');
		$count = $this->getParams('count');
		$questions = $this->getParams('questions');
		$questionsArray = explode(',', $questions);


		$visibleQuestions = CommonQueries::GetQuestionsVisibleToActiveUser();

		/**
		 * @var $lecture \Lecture
		 */
		$lecture = null;


		foreach ($visibleQuestions as $vq)
		{
			$qId = $vq->getId();
			$index = array_search($qId, $questionsArray);
			if ($index !== false)
			{
				array_splice($questionsArray, $index, 1);
				if ($lecture === null)
				{
					$lecture = $vq->getLecture();
				}
				elseif ($lecture->getId() !== $vq->getLecture()->getId())
				{
					return $this->death(StringID::TestCannotContainQuestionsOfDifferentLectures);
				}
			}
		}
		if (count($questionsArray))
		{
			return $this->stop(ErrorCause::invalidInput('Following question IDs are invalid or inaccessible: ' .
				implode(', ', $questionsArray) . '.', 'questions'));
		}
		if ($lecture === null)
		{
			return $this->death(StringID::ChooseAtLeastOneQuestion);
		}

		if (!$this->checkTestGenerationPrivileges($lecture->getId()))
			return $this->death(StringID::InsufficientPrivileges);

		$randomized = $this->generateTest($questions, $count);

		$xtest = new \Xtest();
		$xtest->setDescription($description);
		$xtest->setCount($count);
		$xtest->setLecture($lecture);
		$xtest->setTemplate($questions);
		$xtest->setGenerated(implode(',', $randomized));
		Repositories::persistAndFlush($xtest);
		return true;
	}
}

