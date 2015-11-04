<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Creates or edits attachment.
 * @n @b Requirements: either User::lecturesManageAll privilege, or User::lecturesManageOwn
 *		privilege and be the owner of the lecture this attachment belongs to
 * @n @b Arguments:
 * @li @c id @optional attachment ID (required for edit)
 * @li @c lecture @optional lecture ID (required for add, not editable)
 * @li @c name attachment name (must match attachment ID for edit)
 * @li @c type attachment type
 * @li @c file attachment file
 */
final class EditAttachment extends LectureScript
{
	protected function body ()
	{
		$inputs = array(
			'lecture' => 'isIndex',
			'name' => array(
				'isName',
				'isNotEmpty'
			),
			'type' => array('isEnum' => array('text', 'code', 'image'))
		);
		if (!$this->isInputValid($inputs))
			return false;

		$lectureId = $this->getParams('lecture');
		/** @var \Lecture $lecture */
		$lecture = Repositories::findEntity(Repositories::Lecture, $lectureId);
		$name = $this->getParams('name');
		$type = $this->getParams('type');
		$id = $this->getParams('id');
		$isIdSet = (($id !== null) && ($id !== ''));
		$originalName = $this->getUploadedFileName('file');
		if (!$originalName) { return false; }
		$extensionStart = strrpos($originalName, '.');
		$extension = ($extensionStart === false) ? '' :	substr($originalName, strrpos($originalName, '.'));
		$attachmentFolder = Config::get('paths', 'attachments');
		$filename = $id . '_' . $name . $extension;


		if (!$this->checkTestGenerationPrivileges($lecture))
			return $this->death(StringID::InsufficientPrivileges);
		/**
		 * @var $attachment \Attachment
		 */
		$attachment = null;

		if (!$this->saveUploadedFile('file', $attachmentFolder . $filename))
			return $this->death(StringID::InsufficientPrivileges);

		/** @var \Attachment[] $attachmentsWithThisName */
		$attachmentsWithThisName = Repositories::getRepository(Repositories::Attachment)->findBy(['lecture' => $lectureId, 'name' => $name]);
		if ($isIdSet)
		{
			$attachment = Repositories::findEntity(Repositories::Attachment, $id);
			if (count($attachmentsWithThisName) > 0)
			{
				if ($attachmentsWithThisName[0]->getId() !== $attachment->getId())
				{
					return $this->death(StringID::AttachmentExists);
				}
			}
		}
		else
		{
			if (count($attachmentsWithThisName) > 0)
			{
				return $this->death(StringID::AttachmentExists);
			}
			$attachment = new \Attachment();
		}
		$attachment->setType($type);
		$attachment->setLecture($lecture);
		$attachment->setName($name);
		$attachment->setFile($filename);
		Repositories::persistAndFlush($attachment);
		return true;
	}
}

