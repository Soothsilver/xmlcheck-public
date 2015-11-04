<?php

namespace asm\core;

use asm\core\lang\StringID;
use asm\utils\Security;


/**
 * This request allows the user to reset his or her password after he or she clicked on the reset link.
 */
final class ResetPassword extends DataScript
{
	protected function body ()
	{
		if (!$this->isInputValid(
          [
              'resetLink' => 'isNotEmpty',
              'pass' => 'isNotEmpty'
          ]
        )) {
            return false;
        }

		$resetLink = $this->getParams('resetLink');
        if (strlen($resetLink) < 1)
        {
            // We double-check here. This should not be necessary because the isInputValid function takes care of this.
            // However, if there is a bug in isInputValid that causes the check to be skipped,
            // this will allow the user to change the password of the first user with no resetLink active.
            // This could plausibly be the administrator.
            return $this->death(StringID::HackerError);
        }
        $encryptionType = Security::HASHTYPE_PHPASS;
        $newPassword = $this->getParams('pass');
        $newPasswordHash = Security::hash($newPassword, $encryptionType);
        $usersWithThisResetLink = Repositories::getRepository(Repositories::User)->findBy(['resetLink' => $resetLink]);
        if (count ($usersWithThisResetLink) !== 1)
        {
            return $this->death(StringID::ResetLinkDoesNotExist);
        }
        /**
         * @var $user \User
         */
        $user = $usersWithThisResetLink[0];
        if ($user->getResetLinkExpiry() < new \DateTime())
        {
            return $this->death(StringID::ResetLinkExpired);
        }

        $user->setResetLink('');
        $user->setPass($newPasswordHash);
        Repositories::persistAndFlush($user);
        return true;
	}
}

