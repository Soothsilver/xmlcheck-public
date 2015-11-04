<?php

namespace asm\docs;
require_once __DIR__ . "/JsMemberReplacer.php";

/**
 * Replacer used by WidgetJsInputFilter to for special treatment of class members
 * with certain reserved names.
 */
class WidgetJsMemberReplacer extends JsMemberReplacer
{
	/// @name Replacement types
	//@{
	const T_OPTIONS = 100;	///< widget options
	const T_MAGIC = 101;		///< "magic methods" (to be hidden)
	//@}

	protected $options = array();	///< names of defined widget options

	/**
	 *	@copydoc JsMemberReplacer::addMethodRepl()
	 *
	 * Methods with reserved names '_create', '_init', '_setOption', 'destroy',
	 * and '_set&lt;OPTION_NAME&gt;', where &lt;OPTION_NAME&gt; is name of any
	 * defined widget option, will be hidden instead of being transformed.
	 */
	public function addMethodRepl ($offset, $length, $name, $arguments, $body,
			$doxy = '', $modifiers = 'public', $returnType = '')
	{
		if (($name == '_create') || ($name == '_init') || ($name == 'destroy') || ($name == '_setOption')
				|| (in_array(lcfirst(preg_replace('/^_set/', '', $name)), $this->options)))
		{
			$this->addReplacement(self::T_MAGIC, $offset, $length);
		}
		else
		{
			parent::addMethodRepl($offset, $length, $name, $arguments, $body, $doxy,
					$modifiers, $returnType);
		}
	}

	/**
	 * @copydoc JsMemberReplacer::addPropertyRepl()
	 *
	 * Contents of property 'options' will be transformed to list of class properties
	 * (for easy in-place documenting of widget options which behave a lot like
	 * class properties anyway).
	 */
	public function addPropertyRepl ($offset, $length, $name, $body, $doxy = '',
			$modifiers = 'public', $type = '')
	{
		if ($name == 'options')
		{
			$body = preg_replace('/\}\s*/', '', preg_replace('/^\s*\{/', '', $body));
			$filter = new JsInputFilter();
			$this->addReplacement(self::T_OPTIONS, $offset, $length, array(
				'code' => $filter->parseClassMembers($body, '', false, $this->options),
			));
		}
		else
		{
			parent::addPropertyRepl($offset, $length, $name, $body, $doxy, $modifiers, $type);
		}
	}

	protected function getReplacementString ($type, $data)
	{
		switch ($type)
		{
			case self::T_OPTIONS:
				return $data['code'];
			case self::T_MAGIC:
				return '';
			default:
				return parent::getReplacementString($type, $data);
		}
	}
}

?>