<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Deletes a saved test template.
 * @n @b Requirements: either User::lecturesManageAll privilege, or User::lecturesManageOwn
 *		privilege and be the owner of the lecture this test belongs to
 * @n @b Arguments:
 * @li @c id test ID
 */
final class DeleteTest extends GenTestScript
{
	protected function body ()
	{
		if (!$this->isInputValid(array('id' => 'isIndex')))
			return false;
		$id = $this->getParams('id');
		/**
		 * @var $xtest \Xtest
		 */
		$xtest = Repositories::findEntity(Repositories::Xtest, $id);

		if (!$this->checkTestGenerationPrivileges($xtest->getLecture()->getId()))
			return $this->death(StringID::InsufficientPrivileges);

		Repositories::remove($xtest);
		return true;
	}
}

