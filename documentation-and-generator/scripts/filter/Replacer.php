<?php

namespace asm\docs;

/**
 * Base for code part replacer classes.
 */
abstract class Replacer
{
	private $code;	///< original code
	private $replacements = array();	///< replacements' data

	/**
	 * Sets original source code for this replacer (immutable).
	 * @param string $code
	 */
	public function __construct ($code)
	{
		$this->code = $code;
	}

	/**
	 * Add replacement information to replacement set.
	 * @param mixed $type replacer-specific replacement entity type
	 * @param int $offset offset of code part to be replaced
	 * @param int $length length of code part to be replaced
	 * @param array $data additional data used for creating replacement (specific
	 *		to replacement type)
	 */
	protected final function addReplacement ($type, $offset, $length, $data = array())
	{
		$this->replacements[] = array(
			 'type' => $type,
			 'offset' => $offset,
			 'length' => $length,
			 'data' => $data,
		);
	}

	/**
	 * Gets replacement string for stored replacement type & data.
	 * @param mixed $type replacement type
	 * @param array $data replacement data
	 * @return string replacement of specified code part
	 */
	protected abstract function getReplacementString ($type, $data);

	/**
	 * Transforms code supplied to constructor using stored replacements.
	 * @return string transformed code
	 */
	public final function performReplace ()
	{
		$code = $this->code;
		$diff = 0;
		foreach ($this->replacements as $repl) {
			$replString = $this->getReplacementString($repl['type'], $repl['data']);

//print_r(array(substr($code, $replacement['offset'] + $diff, $replacement['length']), $replacementString));
			$code = substr_replace($code, $replString, $repl['offset'] + $diff,
					$repl['length']);
			$diff += strlen($replString) - $repl['length'];
		}
//exit;

		return $code;
	}

	/**
	 * Turns composite JavaScript identifier to doxygen-like identifier referring
	 * to the same entity.
	 * Sample use:
	 * @code
	 * $this->doxyfiQualifiedName('asm.ui.Config');
	 * @endcode
	 * yields
	 * @code
	 * asm::ui::Config
	 * @endcode
	 * @param string $name
	 * @return string doxyfied name
	 */
	protected final function doxyfiQualifiedName ($name) {
		return str_replace('.', '::', $name);
	}
}

?>