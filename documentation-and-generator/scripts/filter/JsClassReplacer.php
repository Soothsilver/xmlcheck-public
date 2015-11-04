<?php

namespace asm\docs;
require_once __DIR__ . "/Replacer.php";

/**
 * Replacer of object-oriented JavaScript class definitions (-> pseudo-C# code).
 */
class JsClassReplacer extends Replacer
{
	/// @name Replacement types
	//@{
	const T_CLASS = 1;		///< class definition
	const T_IMPLEMENT = 2;	///< declaration of mixin use
	//@}

	/**
	 * Adds class definition replacement.
	 * @param int $offset
	 * @param int $length
	 * @param string $name class name
	 * @param string $namespace namespace
	 * @param string $parent class parent
	 * @param string $body class body code
	 * @param array $mixins "mixins" added to this class
	 * @param string $doxy doxygen documentation block for this class
	 * @param string $modifiers modifiers separated by spaces ('abstract', 'partial', ...)
	 */
	public function addClassRepl ($offset, $length, $name, $namespace, $parent, $body,
			$mixins = array(), $doxy = '', $modifiers = 'public')
	{
		$this->addReplacement(self::T_CLASS, $offset, $length, array(
			 'name' => $name,
			 'namespace' => $namespace,
			 'parent' => $parent,
			 'body' => $body,
			 'mixins' => $mixins,
			 'doxy' => $doxy,
			 'modifiers' => $modifiers,
		));
	}

	/**
	 * Adds declaration of mixin use replacement.
	 * @param int $offset
	 * @param int $length
	 */
	public function addImplementRepl ($offset, $length)
	{
		$this->addReplacement(self::T_IMPLEMENT, $offset, $length);
	}

	protected function getReplacementString ($type, $data)
	{
		switch ($type)
		{
			case self::T_CLASS:
				$extendsDoc = $data['parent']
					? "\n" . '/// @extends ' . $this->doxyfiQualifiedName($data['parent'])
					: '';
				$implementsDoc = '';
				foreach ($data['mixins'] as $mixin)
				{
					$implementsDoc .= "\n" . '/// @implements ' . $this->doxyfiQualifiedName($mixin);
				}
				return ($data['namespace'] != null)
					? "\nnamespace {$data['namespace']}\n{\n{$data['doxy']}{$extendsDoc}{$implementsDoc}\n{$data['modifiers']} class {$data['name']} {\n{$data['body']}\n}}"
					: "\n{$data['doxy']}{$extendsDoc}{$implementsDoc}\n{$data['modifiers']} class {$data['name']} {\n{$data['body']}\n}";
			default:
				return '';
		}
	}
}

?>