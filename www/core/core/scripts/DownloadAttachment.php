<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Gets an attachment file.
 * @n @b Requirements: must be able to access the attachment
 * @n @b Arguments:
 * @li @c id attachment ID
 */
final class DownloadAttachment extends DownloadScript
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
		$file = $attachment->getFile();
		$lecture = $attachment->getLecture();
		if (!$this->authorizedToManageLecture($lecture))
			return $this->death(StringID::InsufficientPrivileges);
		$this->doNotAttach();
		$this->setOutput(Config::get('paths', 'attachments') . $file);
		return true;
	}
}

