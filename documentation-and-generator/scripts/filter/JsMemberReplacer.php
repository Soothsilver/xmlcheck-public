<?php

namespace asm\docs;
require_once __DIR__ . "/Replacer.php";

/**
 * Replacer of object-oriented JavaScript class member definitions (-> pseudo-C#).
 */
class JsMemberReplacer extends Replacer
{
	/// @name Replacement types
	//@{
	const T_METHOD = 1;		///< class method definition
	const T_PROPERTY = 2;	///< class property definition
	const T_SEPARATOR = 3;	///< class member separator
	//@}

	protected $className;	///< name of class replaced member definitions belong to

	/**
	 * Sets class name and original code for this replacer (immutable).
	 * @param string $code
	 * @param string $className
	 */
	public function  __construct ($code, $className)
	{
		$this->className = $className;
		parent::__construct($code);
	}

	/**
	 * Adds replacement of method definition.
	 * @param int $offset
	 * @param int $length
	 * @param string $name method name
	 * @param array $arguments method arguments
	 * @param string $body method body code
	 * @param string $doxy doxygen documentation block for this method
	 * @param string $modifiers modifiers separated by spaces ('abstract', 'static', ...)
	 * @param string $returnType method return type
	 */
	public function addMethodRepl ($offset, $length, $name, $arguments, $body,
			$doxy = '', $modifiers = 'public', $returnType = '')
	{
		$this->addReplacement(self::T_METHOD, $offset, $length, array(
			 'name' => $name,
			 'arguments' => $arguments,
			 'body' => $body,
			 'doxy' => $doxy,
			 'modifiers' => $modifiers,
			 'type' => $returnType,
		));
	}

	/**
	 * Adds replacement of property definition.
	 * @param int $offset
	 * @param int $length
	 * @param string $name property name
	 * @param string $body initial value of property
	 * @param string $doxy doxygen documentation block for this method
	 * @param string $modifiers modifiers separated by spaces ('static', ...)
	 * @param string $type property type
	 */
	public function addPropertyRepl ($offset, $length, $name, $body, $doxy = '',
			$modifiers = 'public', $type = '')
	{
		$this->addReplacement(self::T_PROPERTY, $offset, $length, array(
			 'name' => $name,
			 'body' => $body,
			 'doxy' => $doxy,
			 'modifiers' => $modifiers,
			 'type' => $type,
		));
	}

	/**
	 * Adds replacement of class member separator.
	 * @param int $offset
	 * @param int $length
	 */
	public function addSeparatorRepl ($offset, $length)
	{
		$this->addReplacement(self::T_SEPARATOR, $offset, $length);
	}

	protected function getReplacementString ($type, $data)
	{
		switch ($type)
		{
			case self::T_METHOD:
				$name = ($data['name'] == 'constructor') ? $this->className : $data['name'];
				$arguments = array();
				foreach ($data['arguments'] as $argName => $argType) {
					$arguments[] = (($argType !== null) ? "$argType " : '') . "$argName";
				}
				$arguments = implode(', ', $arguments);
				return "{$data['doxy']}\n\t{$data['modifiers']} {$data['type']} {$name}({$arguments}) {$data['body']}\n";
			case self::T_PROPERTY:
				if ($data['name'] == 'constructor')
				{
					return '';
				}
				if (preg_match('/^\s*{/', $data['body']))
				{
					return "{$data['doxy']}\n\t{$data['modifiers']} {$data['type']} {$data['name']};\n";
				}
				return "{$data['doxy']}\n\t{$data['modifiers']} {$data['type']} {$data['name']} = {$data['body']};\n";
			default:
				return '';
		}
	}
}

?>