<?php

namespace asm\docs;
require_once __DIR__ . "/Replacer.php";

/**
 * Replacer of pseudo-doxygen documentation entity templates (-> real doxygen entities).
 */
class JsDoxyReplacer extends Replacer
{
	/// @name Replacement types
	//@{
	const T_PARAM = 1;	///< function argument documentation
	const T_RETURN = 2;	///< function return value documentation
	const T_TYPE = 3;		///< variable type documentation
	//@}

	/**
	 * Adds function argument documentation template replacement.
	 * @param int $offset
	 * @param int $length
	 * @param string $name argument name
	 */
	public function addParamRepl ($offset, $length, $name)
	{
		$this->addReplacement(self::T_PARAM, $offset, $length, array(
			 'name' => $name,
		));
	}

	/**
	 * Adds function return value documentation template replacement.
	 * @param int $offset
	 * @param int $length
	 */
	public function addReturnRepl ($offset, $length)
	{
		$this->addReplacement(self::T_RETURN, $offset, $length);
	}

	/**
	 * Adds variable type documentation template replacement.
	 * @param int $offset
	 * @param int $length
	 */
	public function addTypeRepl ($offset, $length)
	{
		$this->addReplacement(self::T_TYPE, $offset, $length);
	}

	protected function getReplacementString ($type, $data)
	{
		switch ($type)
		{
			case self::T_PARAM:
				return "@param {$data['name']}";
			case self::T_RETURN:
				return '@return';
			default:
				return '';
		}
	}
}

?>