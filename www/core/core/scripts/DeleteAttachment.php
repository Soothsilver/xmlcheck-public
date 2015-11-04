<?php

namespace asm\core;
use asm\utils\Filesystem;

/**
 * @ingroup requests
 * Deletes attachment (with questions).
 * @n @b Requirements: either User::lecturesManageAll, or User::lecturesManageOwn and
 *		be the owner of the lecture this attachment belongs to
 * @n @b Arguments:
 * @li @c id attachment ID
 */
final class DeleteAttachment extends LectureScript
{
	protected function body ()
	{
		if (!$this->isInputValid(array('id' => 'isIndex')))
			return false;

		$id = $this->getParams('id');
		/**
		 * @var $attachment \Attachment
		 */
		$attachment = Repositories::findEntity(Repositories::Attachment, $id);

		if (!$this->authorizedToManageLecture($attachment->getLecture()))
			return false;

		$folder = Config::get('paths', 'attachments');
		$file = $attachment->getFile();

		RemovalManager::deleteAttachmentById($id);
		Filesystem::removeFile($folder . $file);
		return true;
	}
}

