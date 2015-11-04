<?php

namespace asm\core;
use asm\core\lang\Language;
use asm\core\lang\StringID;

/**
 * @ingroup requests
 * Opens new user session if supplied credentials match existing user account.
 * @n @b Requirements: none
 * @n @b Arguments:
 * @li @c name username
 * @li @c pass password (must match username)
 * 
 * @see User
 */
final class Login extends DataScript
{
	protected function body ()
	{
		if (!$this->isInputSet(array('name', 'pass')))
			return false;

        $user = User::instance();

		if (!$user->login($this->getParams('name'), $this->getParams('pass')))
			return $this->stop(Language::get(StringID::InvalidLogin));

		$this->setOutput(array(
			'id' => $user->getId(),
			'username' => $user->getName(),
			'name' => $user->getRealName(),
			'email' => $user->getEmail(),
			'lastLogin' => $user->getLastAccess(),
			'privileges' => $user->unpackPrivileges(),
			'timeout' => $user->getTimeout(),
            User::sendEmailOnSubmissionRatedStudent => $user->getData(User::sendEmailOnSubmissionRatedStudent),
            User::sendEmailOnSubmissionConfirmedTutor => $user->getData(User::sendEmailOnSubmissionConfirmedTutor),
            User::sendEmailOnAssignmentAvailableStudent => $user->getData(User::sendEmailOnAssignmentAvailableStudent),
		));
		return true;
	}
}

