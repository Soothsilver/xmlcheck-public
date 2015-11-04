<?php

namespace asm\docs;
require_once __DIR__ . "/JsInputFilter.php";

/**
 * Input filter for object-oriented JavaScript sources using jQuery widget factory.
 * Supports static class members (if defined in same file).
 * Doesn't support inheritance.
 * Widget options are shown in documentation as public class properties.
 */
class WidgetJsInputFilter extends JsInputFilter
{
	protected $memberReplacerClass = '\asm\docs\WidgetJsMemberReplacer';

	public function apply ($code)
	{
		$qNameRx = $this->getQualifiedIdentifierRegex();
		$doxyRx = $this->getDoxydocRegex();
		if (!preg_match("|($doxyRx)?\\s*^(\\\$\\.widget\\('($qNameRx)'\\s*,\\s*{)(\$)|m",
				$code, $matches, PREG_OFFSET_CAPTURE)) {
			return null;
		}

		list($match, $doxyDoc, $widgetDecl, $widgetClass) = $matches;
		list($class, $namespace) = $this->splitQualifiedName($widgetClass[0]);

		$namespace = 'widget';

		$offset = $match[1];

		$bodyStart = $this->getAfterOffset($widgetDecl);
		$body = $this->parseClassBody(substr($code, $bodyStart), $class, $overflow);

		if (preg_match("|^\\$\\.fn\\.extend\\(\\$.{$widgetClass[0]}\\s*,\\s*\\{|m",
				$overflow, $matches1, PREG_OFFSET_CAPTURE)) {
			$staticPartOffset = $this->getAfterOffset($matches1[0]);
			$body .= $this->parseClassBody(substr($overflow, $staticPartOffset), $class,
					$overflow, self::CLASS_BODY_STATIC);
		}

		$replacer = new JsClassReplacer($code);

		$overflowOffset = strlen($code) - strlen($overflow);
		$replacer->addClassRepl($offset, $overflowOffset - $offset, $class, $namespace,
				null, $body, array(), $doxyDoc[0]);

		return $replacer->performReplace();
	}
}

?>