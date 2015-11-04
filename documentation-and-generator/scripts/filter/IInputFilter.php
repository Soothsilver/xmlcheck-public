<?php

namespace asm\docs;

/**
 * Input filter interface.
 * @see InputFilterScript
 */
interface IInputFilter
{
	/**
	 * Transforms supplied code with filter-specific transformations.
	 * @param string $code documented code
	 * @return mixed transformed @a $code (string) or null if code could not be
	 *		parsed with this filter
	 */
	public function apply ($code);
}

?>