<?php

namespace asm\core;
use asm\core\lang\StringID;

use asm\utils\Security;

/**
 * @ingroup requests
 * Creates or edits user.
 * @n @b Requirements: none to create inactive account of default type, User::usersAdd
 *		privilege to create active account of custom type, User::usersManage privilege
 *		to edit user account
 * @n @b Arguments:
 * @li @c id @optional user ID (required for edit)
 * @li @c type @optional (required for creation of active user account)
 * @li @c name username (must match user ID for edit)
 * @li @c realName user's real name
 * @li @c email
 * @li @c pass account password
 * @li @c repass must match ^
 *
 * Notes on user creation:
 * If user session is currently open, this handler tries to create full-fledged
 * active user, otherwise it tries to register new user of base type and send
 * them e-mail with activation code. They must activate their user account before
 * they can use it.
 */
final class EditUser extends DataScript
{
    /**
     * Runs this script.
     * @return bool Is it successful?
     * @throws \Exception Should never occur.
     */
    protected function body ()
	{
		$inputs = array(
			'name' => array(
				'isAlphaNumeric',
				'hasLength' => array(
					'min_length' => Constants::UsernameMinLength,
					'max_length' => Constants::UsernameMaxLength,
				),
			),
			'realname' => array(
				'isNotEmpty',
				'isName',
			),
			'email' => 'isEmail',
			'pass' => array(),
			'repass' => array(),
		);
		if (!$this->isInputValid($inputs))
			return false;

        // Extract input data
        $username = strtolower( $this->getParams('name') );
        $realname = $this->getParams('realname');
        $email = $this->getParams('email');
        $pass = $this->getParams('pass');
        $repass = $this->getParams('repass');
        $id = $this->getParams('id');
        $type = $this->getParams('type');
        $user = null;
        $isIdSet = ($id !== null && $id !== '');
        $isTypeSet = ($type !== null && $type !== '');

        // Extract database data
        if ($id)
        {
            $user = Repositories::findEntity(Repositories::User, $id);
        }
        $userExists = ($user != null);
        $sameNameUserExists = count(Repositories::getRepository(Repositories::User)->findBy(['name' => $username])) > 0;

        // Custom verification of input data
        if ($pass !== $repass)
        {
            return $this->death(StringID::InvalidInput);
        }
        if ($userExists)
        {
            if ((strlen($pass) < Constants::PasswordMinLength || strlen($pass) > Constants::PasswordMaxLength) && $pass !== "")
            {
                return $this->death(StringID::InvalidInput);
            }
        }
        else
        {
            // A new user must have full password
            if (strlen($pass) < Constants::PasswordMinLength || strlen($pass) > Constants::PasswordMaxLength)
            {
                return $this->death(StringID::InvalidInput);
            }
        }

        $code = '';
        $unhashedPass = $pass;
        $pass = Security::hash($pass, Security::HASHTYPE_PHPASS);
        $canAddUsers = User::instance()->hasPrivileges(User::usersAdd);
        $canEditUsers = User::instance()->hasPrivileges(User::usersManage);
        $isEditingSelf = ($id == User::instance()->getId()); // This must not be a strict comparison.
        /**
         * @var $user \User
         */

        if (!$userExists && !$sameNameUserExists) // create/register new user
        {
            if ($this->getParams('fromRegistrationForm'))
            {
                if ($type != Repositories::StudentUserType)
                    return $this->death(StringID::InsufficientPrivileges);

                $code = md5(uniqid(mt_rand(), true));
                $emailText = file_get_contents(Config::get("paths", "registrationEmail"));
                $emailText = str_replace( "%{Username}", $username, $emailText);
                $emailText = str_replace( "%{ActivationCode}", $code, $emailText);
                $emailText = str_replace( "%{Link}", Config::getHttpRoot() . "#activate", $emailText);
                $lines = explode("\n", $emailText);
                $subject = $lines[0]; // The first line is subject.
                $text = preg_replace('/^.*\n/', '', $emailText); // Everything except the first line.

                $returnCode = Core::sendEmail($email, $subject, $text);

                if (!$returnCode)
                    return $this->stop(ErrorCode::mail, 'user registration failed', 'email could not be sent');
            }
            else if (!$canAddUsers)
            {
                return $this->death(StringID::InsufficientPrivileges);
            }
            $user = new \User();
            /** @var \UserType $typeEntity */
            $typeEntity = Repositories::findEntity(Repositories::UserType, $type);
            $user->setType($typeEntity);
            $user->setPass($pass);
            $user->setName($username);
            $user->setEmail($email);
            $user->setActivationCode($code);
            $user->setEncryptionType(Security::HASHTYPE_PHPASS);
            $user->setRealName($realname);
            Repositories::persistAndFlush($user);
        }
        elseif ($isIdSet) // edit existing user
        {
            if (!$canEditUsers && ($isTypeSet || (!$isEditingSelf)))
                return $this->stop(ErrorCode::lowPrivileges, 'cannot edit data of users other than yourself');

            $type = $isTypeSet ? $type : $user->getType()->getId();
            /** @var \UserType $typeEntity */
            $typeEntity = Repositories::findEntity(Repositories::UserType, $type);

            if ($unhashedPass)
            {
                $user->setPass($pass);
                $user->setEncryptionType(Security::HASHTYPE_PHPASS);
            }
            $user->setType($typeEntity);
            $user->setEmail($email);
            $user->setActivationCode('');
            $user->setRealName($realname);
            Repositories::persistAndFlush($user);
        }
        else
        {
            return $this->death(StringID::UserNameExists);
        }
        return true;
	}
}

