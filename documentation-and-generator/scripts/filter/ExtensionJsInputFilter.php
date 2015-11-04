<?php

namespace asm\docs;
require_once __DIR__ . "/JsInputFilter.php";

/**
 * Input filter for extensions of core JavaScript classes and jQuery.
 */
class ExtensionJsInputFilter extends JsInputFilter
{
	public function apply ($code)
	{
		$qNameRx = $this->getQualifiedIdentifierRegex();
		$doxyRx = $this->getDoxydocRegex();
		if (!preg_match("|($doxyRx)?\\s*^(\\\$(\\.fn)?\\.extend\\((($qNameRx)\\.prototype\\s*,)?\\s*{)(\$)|m",
				$code, $matches, PREG_OFFSET_CAPTURE)) {
			return null;
		}

		list($match, $doxyDoc, $extDecl, $jQueryFnToken, $extClassToken, $extClass) = $matches;
		$offset = $match[1];

		list($class, $namespace) = ($extClassToken[1] > -1)
				? $this->splitQualifiedName($extClass[0])
				: array('jQuery', null);

		$bodyStart = $this->getAfterOffset($extDecl);
		$body = $this->parseClassBody(substr($code, $bodyStart), $class, $overflow,
				($jQueryFnToken[1] < 0) ? self::CLASS_BODY_STATIC : self::CLASS_BODY_NONE);
		$overflowOffset = strlen($code) - strlen($overflow);

		$replacer = new $this->classReplacerClass($code);
		$replacer->addClassRepl($offset, $overflowOffset - $offset, $class,
				$namespace, null, $body, array(), $doxyDoc[0], 'public partial');

		return $replacer->performReplace();
	}
}

?>