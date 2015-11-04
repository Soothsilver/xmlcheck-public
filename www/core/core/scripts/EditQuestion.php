<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Creates or edits a test question.
 * @n @b Requirements: either User::lecturesManageAll privilege, or User::lecturesManageOwn
 *		privilege and be the owner of the lecture this question belongs to
 * @n @b Arguments:
 * @li @c id @optional question ID (required for edit)
 * @li @c lecture @optional lecture ID (required for add, not editable)
 * @li @c text question text
 * @li @c type question type (must be one of 'text', 'choice', 'multi')
 * @li @c options @optional a set of options for certain question types
 */
final class EditQuestion extends LectureScript
{
	protected function body ()
	{
		$inputs = array(
			'lecture' => 'isIndex',
			'text' => 'isNotEmpty',
			'type' => array('isEnum' => array('text', 'choice', 'multi')),
		);
		if (!$this->isInputValid($inputs))
			return false;


		$lectureId = $this->getParams('lecture');
		$text = $this->getParams('text');
		$type = $this->getParams('type');
		$id = $this->getParams('id');
		$isIdSet = (($id !== null) && ($id !== ''));

		$options = $this->getParams('options') . '';
		$attachments = $this->getParams('attachments') . '';

		if (!$this->checkTestGenerationPrivileges($lectureId))
			return $this->death(StringID::InsufficientPrivileges);

		$visibleAttachments = CommonQueries::GetAttachmentsVisibleToActiveUser();

		$attTmp = $attachments ? explode(';', $attachments) : array();
		foreach ($visibleAttachments as $va)
		{
			$aId = $va->getId();
			$index = array_search($aId, $attTmp);
			if ($index !== false)
			{
				array_splice($attTmp, $index, 1);
				if ($va->getLecture()->getId() != $lectureId)
					return $this->death(StringID::AttachmentBelongsToAnotherLecture);
			}
		}

		if (count($attTmp))
		{
			return $this->stop(ErrorCause::invalidInput('Following attachment IDs are invalid or inaccessible: ' .
					implode(', ', $attTmp) . '.', 'attachments'));
		}

		/** @var \Question $question */
		$question = null;
		if (!$isIdSet)
		{
			$question = new \Question();
		}
		else {
			$question = Repositories::findEntity(Repositories::Question, $id);
			if ($question->getLecture()->getId() != $lectureId) { // This must be a weak comparison, because lectureId comes from user and is text.
				return $this->death(StringID::HackerError);
			}
		}
		$question->setAttachments($attachments);
		/** @var \Lecture $lecture */
		$lecture = Repositories::findEntity(Repositories::Lecture, $lectureId);
		$question->setLecture($lecture);
		$question->setOptions($options);
		$question->setText($text);
		$question->setType($type);
		Repositories::persistAndFlush($question);
		return true;
	}
}

