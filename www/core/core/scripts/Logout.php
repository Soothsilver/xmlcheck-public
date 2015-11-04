<?php

namespace asm\core;

/**
 * @ingroup requests
 * Closes current user session.
 * @n @b Requirements: none
 * @n @b Arguments: none
 *
 * @see User
 */
final class Logout extends DataScript
{
	protected function body ()
	{
		if (!User::instance()->logout())
			return $this->stop('logout unsuccessful');
		return true;
	}
}

