<?php

namespace asm\core;


use asm\core\lang\StringID;

use asm\utils\StringUtils;

/**
 * This request allows the user to have a password reset link sent to him or her.
 */
final class RequestResetLink extends DataScript
{
	protected function body ()
	{
		if (!$this->isInputSet(array('email')))
			return false;

		$email = $this->getParams('email');

        $users = Repositories::getRepository(Repositories::User)->findBy(['email' => $email]);
        foreach($users as $user)
        {
            /**
             * @var $user \User
             */

            // Generate reset link.
            $resetLink = StringUtils::randomString(60);
            $now = new \DateTime();
            $expiryDate = $now->add(new \DateInterval('P1D'));

            // Add in in the database (replacing any older reset links in the process)
            $user->setResetLink($resetLink);
            $user->setResetLinkExpiry($expiryDate);
            Repositories::persistAndFlush($user);

            // Send the e-mail
            $body = "A Password Reset Link was requested for your e-mail address on XMLCheck.\n\nYour name: " . $user->getRealName() . "\nYour login: " . $user->getName() . "\n\nClick this link to reset your password: \n\n" . Config::get('roots', 'http') . "#resetPassword#" . $resetLink . "\n\nThe link will be valid for the next 24 hours, until " . $expiryDate->format("Y-m-d H:i:s") . ".";

            if (!Core::sendEmail($user->getEmail(), "[XMLCheck] Password Reset Link for '" . $user->getRealName() . "'", $body))
            {
                return $this->death(StringID::MailError);
            }

        }
        $this->addOutput('count', count($users));
        return true;
	}
}

