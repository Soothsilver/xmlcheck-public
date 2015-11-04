<?php

namespace asm\docs;
require_once __DIR__ . "/JsInputFilter.php";

/**
 * Input filter for object-oriented JavaScript code using Base.js library for
 * inheritance simulation.
 * Supports 'extend' and 'implement' declarations and static class members.
 */
class BaseJsInputFilter extends JsInputFilter
{
	public function apply ($code)
	{
		$qNameRx = $this->getQualifiedIdentifierRegex();
		$doxyRx = $this->getDoxydocRegex();
		if (!preg_match("|($doxyRx)?\\s*^(($qNameRx)\\s*=\\s*($qNameRx)\\.extend\\({)(\$)|m",
				$code, $matches, PREG_OFFSET_CAPTURE))
		{
			return null;
		}

		list($match, $doxyDoc, $classDecl, $className, $parentName) = $matches;
		list($class, $namespace) = $this->splitQualifiedName($className[0]);
		$parent = $this->stripNamespace($parentName[0], $namespace);
		$parent = ($parent == 'Base') ? null : $parent;
		$offset = $match[1];
		$bodyStart = $this->getAfterOffset($classDecl);

		$body = $this->parseClassBody(substr($code, $bodyStart), $class, $overflow,
				self::CLASS_BODY_COMPOSITE);
		$overflowOffset = strlen($code) - strlen($overflow);
		
		$mixins = array();
		$implMatches = array();
		$cNameRx = preg_quote($className[0], '/');
		if (preg_match_all("/^$cNameRx\\.implement\\(($qNameRx)\\);(\$)/m", $overflow,
				$implements, PREG_SET_ORDER | PREG_OFFSET_CAPTURE))
		{
			foreach ($implements as $implement)
			{
				list($implMatch, $interface) = $implement;
				$mixins[] = $this->stripNamespace($interface[0], $namespace);
				$implMatches[] = $implMatch;
			}
		}

		$replacer = new $this->classReplacerClass($code);

		$modifiers = 'public' . ((strpos($doxyDoc[0], '@mixin') !== false) ? ' abstract' : '');
		$replacer->addClassRepl($offset, $overflowOffset - $offset, $class, $namespace,
				$parent, $body, $mixins, $doxyDoc[0], $modifiers);

		foreach ($implMatches as $implMatch)
		{
			$replacer->addImplementRepl($overflowOffset + $implMatch[1], strlen($implMatch[0]));
		}

		return $replacer->performReplace();
	}
}

?>