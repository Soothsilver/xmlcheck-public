<?php

namespace asm\docs;
require_once __DIR__ . '/IInputFilter.php';

/**
 * Makes transformations on Java files to make their documentation comments valid for Doxygen.
 */
class JavaInputFilter implements IInputFilter
{
	public function apply ($code)
	{
		// Due to a Doxygen bug (https://bugzilla.gnome.org/show_bug.cgi?id=737444), annotations cause a class to remain undocumented in the generated documentation. The bug is supposed to be fixed in the next version of Doxygen to be released (1.8.9). Until then:
		$code = preg_replace('/@SuppressWarnings\([^)]+\)/', '', $code);
		return $code;
	}
}

?>