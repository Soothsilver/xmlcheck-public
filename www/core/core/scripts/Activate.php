<?php

namespace asm\core;
use asm\core\lang\StringID;

/**
 * @ingroup requests
 * Activates user account with supplied activation code.
 * @n @b Requirements: none
 * @n @b Arguments:
 * @li @c code user account activation code
 */
final class Activate extends DataScript
{
	protected function body ()
	{
		if (!$this->isInputSet(array('code')))
			return false;

		$code = $this->getParams('code');

		/**
		 * @var $users \User[]
		 */
		$users = Repositories::getRepository(Repositories::User)->findBy(['activationCode' => $code]);
		if (count($users) === 1)
		{
			$users[0]->setActivationCode('');
			Repositories::persistAndFlush($users[0]);
			return true;
		}
		else
		{
			return $this->death(StringID::InvalidActivationCode);
		}
	}
}

