<?php

namespace asm\docs;
require_once __DIR__ . '/IInputFilter.php';

/**
 * Makes transformations on PHP files to make their documentation comments valid for Doxygen.
 */
class PhpInputFilter implements IInputFilter
{
	private function tamasimreiFilter($code)
	{
		$returnText = "";
		$tokens = token_get_all($code);
		$buffer = null;
		foreach ($tokens as $token) {
			if (is_string($token)) {
				if ((! empty($buffer)) && ($token == ';')) {
					$returnText .= $buffer;
					unset($buffer);
				}
				$returnText .= $token;
			} else {
				list($id, $text) = $token;
				switch ($id) {
					case T_DOC_COMMENT :
						$text = addcslashes($text, '\\');
						$returnText .= $text;
						break;
					default:
						if ((! empty($buffer))) {
							$buffer .= $text;
						} else {
							$returnText .= $text;
						}
						break;
				}
			}
		}
		return $returnText;
	}
	public function apply ($code)
	{
		// Transforms PHPDoc documentation to doxygen.
		// param type $name comment -> param $name (type) comment
  		// param type [...] comment -> param [...] (type) comment
		//	 $code = preg_replace('/(\*\s@param(\[(in|out|in,out)\])?\s)([^$&[][a-zA-Z0-9]*)(\s)(&?\$[a-zA-Z0-9]+|\[\.\.\.\])/', '$1$6$5($4)', $code);

		// Bypasses doxygen's bug that causes variable declarations not to be displayed.
		 $code = str_replace('@var', '', $code);

		$code = $this->tamasimreiFilter($code);

		// Left from Konopasek's filter
		$code = preg_replace('/(@return\s)([a-zA-Z]+)(\s)/', '$1($2)$3', $code);
		$code = preg_replace('/^\s*use\s+[a-zA-Z0-9_\\\\]+(\s*,\s*[a-zA-Z0-9_\\\\]+)*;\s*$/m', '', $code);
		return $code;
	}
}

?>