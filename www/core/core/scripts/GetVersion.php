<?php

namespace asm\core;

/**
 * @ingroup requests
 * Retrieves application version
 * @n @b Requirements: none
 * @n @b Arguments: none
 */
final class GetVersion extends DataScript
{
	protected function body ()
	{
		$this->addOutput('version', implode('.', Config::get('version')));
	}
}

