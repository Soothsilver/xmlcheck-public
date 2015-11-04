<?php

namespace asm\docs;

require_once __DIR__ . "/IInputFilter.php";
require_once __DIR__ . "/JsClassReplacer.php";
require_once __DIR__ . "/JsMemberReplacer.php";
require_once __DIR__ . "/JsDoxyReplacer.php";
require_once __DIR__ . "/WidgetJsMemberReplacer.php";

/**
 * Base for transformers of object-oriented JavaScript code to C#-like pseudo-code
 * compatible with doxygen.
 */
class JsInputFilter implements IInputFilter
{
	const CLASS_BODY_NONE		= 0;		///< "no flag" dummy for parseClassBody()
	const CLASS_BODY_STATIC		= 0x1;	///< "all properties are static" flag
	/**
	 * flag indicating that the class definition is composed of two object literals,
	 * first one with instance members, second with static members
	 * @note With CLASS_BODY_STATIC set, all members are static.
	 */
	const CLASS_BODY_COMPOSITE	= 0x2;

	/// class for replacement of doxygen documentation blocks of class members
	protected $doxyReplacerClass = '\asm\docs\JsDoxyReplacer';
	/// class for replacement of class body contentsc
	protected $memberReplacerClass = '\asm\docs\JsMemberReplacer';
	/// class for replacement of whole class definition
	protected $classReplacerClass = '\asm\docs\JsClassReplacer';

	/**
	 * @copydoc IInputFilter::apply()
	 * 
	 * @note Dummy implementation (this class can be used to access public
	 * methods, but not to do any actual filtering).
	 */
	public function apply ($code) {
		return null;
	}

	/**
	 * Returns regular expression part matching simple JavaScript identifier.
	 * Contains no capturing groups.
	 * @return string see method description
	 */
	protected final function getIdentifierRegex () {
		return '[_a-zA-Z][_a-zA-Z0-9]*';
	}

	/**
	 * Returns regular expression part matching composite JavaScript identifier.
	 * Contains no capturing groups.
	 * @return string see method description
	 */
	protected final function getQualifiedIdentifierRegex () {
		$identRegex = $this->getIdentifierRegex();
		return "$identRegex(?:\.$identRegex)*";
	}

	/**
	 * Returns regular expression part matching doxygen documentation block.
	 * Contains no capturing groups.
	 * @note Doesn't match single-line comment blocks.
	 * @return string see method description
	 */
	protected final function getDoxydocRegex () {
		return '/\\*\\*(?:[^*](?:\\*[^/])?)+\\*/';
	}

	/**
	 * Splits composite JavaScript identifier into base identifier and parent
	 * object name ("namespace").
	 * Sample use:
	 * @code
	 * $this->splitQualifiedName('asm.ui.Config');
	 * @endcode
	 * yields
	 * @code
	 * Array(
	 *		[0] => Config
	 *		[1] => asm.ui
	 * )
	 * @endcode
	 * @param string $name composite JavaScript identifier
	 * @return array array with two elements, base and parent (see method description)
	 */
	protected final function splitQualifiedName ($name) {
		$parts = explode('.', $name);
		return array(array_pop($parts), implode('.', $parts));
	}

	/**
	 * Checks whether supplied string starts with supplied substring.
	 * @param string $string
	 * @param string $pattern substring to match against @a $string beginning
	 * @return bool true if @a $string begins with @a $pattern
	 */
	protected final function stringStartsWith ($string, $pattern) {
		return (strcmp(substr($string, 0, strlen($pattern)), $pattern) === 0);
	}

	/**
	 * Strips specified prefix from supplied string.
	 * @param string $string
	 * @param string $prefix
	 * @return string @a $string with @a $prefix removed from the beginning (or
	 *		whole @a $string if it doesn't begin with @a $prefix)
	 */
	protected final function stripPrefix ($string, $prefix) {
		if ($this->stringStartsWith($string, $prefix))
		{
			return substr($string, strlen($prefix));
		}
		return $string;
	}

	/**
	 * Strips "namespace" from JavaScript identifier.
	 * @param string $string identifier
	 * @param string $namespace name of namespace
	 * @return string @a $string with @a $namespace followed by dot removed from
	 *		the beginning
	 */
	protected final function stripNamespace ($string, $namespace) {
		return $this->stripPrefix($string, $namespace . '.');
	}

	/**
	 * Gets offset of first character after match captured by preg_match() or
	 * preg_match_all() with offset capturing on.
	 * @param array $match array with two elements - matched substring and its offset
	 *		in original string
	 * @return int offset of first character after the mached substring
	 */
	protected final function getAfterOffset ($match) {
		return $match[1] + strlen($match[0]);
	}

	/**
	 * Parses and transforms templated doxygen-like documentation block of class method.
	 * Block can contain all doxygen-compatible code and two additional tags
	 * @c \@tparam and @c \@treturn. Both of them accept argument/return type as
	 * additional first argument and are transformed to \@param and \@return.
	 * Sample use:
	 * @code
	 * / **
	 *  * \@tparam string doxyCode doxygen documentation block code
	 *  * \@tparam mixed [...] additional data (<- denotes variable number of arguments)
	 *  * \@treturn string transformed documentation block
	 *  * /
	 * @endcode
	 * will be transformed to
	 * @code
	 * / **
	 *  * \@param doxyCode doxygen documentation block code
	 *  * \@param [...] additional data (<- denotes variable number of arguments)
	 *  * \@return transformed documentation block
	 *  * /
	 * @endcode
	 * @note Doesn't work with "single-line" comment blocks.
	 * @param string $doxyCode doxygen documentation block code
	 * @param[out] mixed $type return type (or null if not specified)
	 * @param[out] array $arguments method argument types
	 * @return string transformed documentation block
	 */
	public function parseMethodDoxy ($doxyCode, &$type, &$arguments) {
		$type = null;
		$arguments = array();

		$replacer = new $this->doxyReplacerClass($doxyCode);

		$nameRx = $this->getIdentifierRegex();
		preg_match_all("/@t(param|return)\\s+($nameRx)(\\s+($nameRx|\[\.\.\.\]))?/",
				$doxyCode, $templTags, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		foreach ($templTags as $templTag)
		{
			list($tagMatch, $tagName, $tagParam1, $tagParam2Wrapper, $tagParam2) = $templTag;
			$offset = $tagMatch[1];
			$length = strlen($tagMatch[0]);

			if ($tagName[0] == 'param')
			{
				if (!$this->stringStartsWith($tagParam2[0], '['))
				{
					$arguments[$tagParam2[0]] = $tagParam1[0];
				}
				$replacer->addParamRepl($offset, $length, $tagParam2[0]);
			}
			else
			{
				$type = $tagParam1[0];
				$replacer->addReturnRepl($offset, $length - strlen($tagParam2Wrapper[0]));
			}
		}

		return $replacer->performReplace();
	}

	/**
	 * Parses and transforms templated doxygen-like documentation block of class property.
	 * Block can contain all doxygen-compatible code and one additional tag
	 * @c \@type. It accepts single parameter and is removed during transformation.
	 * Sample use:
	 * @code
	 * / ** \@type string
	 *  * header icon
	 *  * /
	 * @endcode
	 * will be transformed to
	 * @code
	 * / **
	 *  * header icon
	 *  * /
	 * @endcode
	 * @note Doesn't work with "single-line" comment blocks.
	 * @param string $doxyCode doxygen documentation block code
	 * @param[out] mixed $type property type (or null if not specified)
	 * @return string transformed documentation block
	 */
	public function parsePropertyDoxy ($doxyCode, &$type) {
		$type = null;

		$replacer = new $this->doxyReplacerClass($doxyCode);
		
		$nameRx = $this->getIdentifierRegex();
		if (preg_match("/@type\\s+($nameRx)/", $doxyCode, $templTag, PREG_OFFSET_CAPTURE))
		{
			$type = $templTag[1][0];
			$replacer->addTypeRepl($templTag[0][1], strlen($templTag[0][0]));
		}

		return $replacer->performReplace();
	}

	/**
	 * Filters set of matches from preg_match_all(), leaving only those with the
	 * lowest indentation.
	 * Can be used for simple code "parsing" without the need to really break it
	 * into syntax entities.
	 * @param array $matches matches from preg_match_all() with offset capturing on
	 * @param int $indentIndex index of capturing group containing match indentation
	 *		in original regular expression
	 * @return array filtered @a $matches
	 */
	protected final function filterMatchesByIndent ($matches, $indentIndex) {
		$filtered = array();
		$correctIndent = null;
		foreach ($matches as $match)
		{
			$indent = strlen($match[$indentIndex][0]);
			if ($correctIndent === null)
			{
				$correctIndent = $indent;
			}
			if ($indent == $correctIndent)
			{
				$filtered[] = $match;
			}
		}
		return $filtered;
	}

	/**
	 * Parses contents of JavaScript object literal and transforms it to set of
	 * pseudo-C# class member definitions.
	 * Doxygen documentation blocks preceding the object properties will be used
	 * as class member documentation. Some of their contents will be also used
	 * for the transformation.
	 * See JavaScript sources for examples.
	 * @note Single-line doxygen comments will not be preserved (use multi-line
	 *		comment syntax instead).
	 * @param string $code object literal content (without surrounding parens)
	 * @param string $className name of the class these members will belong to
	 * @param bool $static true if all members are static
	 * @param[out] mixed $index supply to get array with names of all found members
	 * @return string transformed code
	 */
	public function parseClassMembers ($code, $className, $static = false, &$index = null) {
		$createIndex = ($index !== null);
		if ($createIndex)
		{
			$index = array();
		}

		$doxyRx = $this->getDoxydocRegex();
		$nameRx = $this->getIdentifierRegex();
		$memberDeclRx = "|(?:(,$)[ \t\n]*)?($doxyRx)?\\s*^(\\t+)($nameRx)\\s*:\\s*(function(?:\\s+$nameRx)?\\s*\\(($nameRx(?:,\\s*$nameRx)*)?\\))?|m";
		preg_match_all($memberDeclRx, $code, $dirtyMatches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

		$replacer = new $this->memberReplacerClass($code, $className);
		$matches = $this->filterMatchesByIndent($dirtyMatches, 3);
		
		for ($i = 0; $i < count($matches); ++$i)
		{
			list($match, $memberSeparator, $doxyDoc, $indent, $memberName,
					$methodDecl, $methodArguments) = $matches[$i];

			if ($memberSeparator[1] > -1)
			{
				$replacer->addSeparatorRepl($memberSeparator[1], strlen($memberSeparator[0]));
			}
			
			$matchLength = strlen($match[0]);
			$afterMatchOffset = $this->getAfterOffset($match);
			$body = isset($matches[$i + 1])
					? substr($code, $match[1] + $matchLength, $matches[$i + 1][0][1] - $afterMatchOffset)
					: substr($code, $match[1] + $matchLength);

			$doxyOffset = ($doxyDoc[1] > -1) ? $doxyDoc[1] : null;
			$doxyCode = ($doxyOffset !== null) ? $doxyDoc[0] : null;
			
			$name = $memberName[0];
			if ($createIndex)
			{
				$index[] = $name;
			}

			$modifiers = $this->stringStartsWith($memberName[0], '_') ? 'protected' : 'public';
			$modifiers .= $static ? ' static' : '';
			$offset = (($doxyOffset !== null) ? $doxyOffset : $memberName[1]);
			$headerLength = $afterMatchOffset - $offset;
			$length = $headerLength + strlen($body);
			if ($methodDecl[1] > -1)
			{
				$arguments = array();
				$doxy = '';
				$returnType = '';
				if ($doxyCode !== null)
				{
					$doxy = $this->parseMethodDoxy($doxyCode, $returnType, $arguments);
					$arguments = array_merge(array_fill_keys(preg_split('/,\s*/',
							$methodArguments[0], null, PREG_SPLIT_NO_EMPTY), null), $arguments);
				}

				$replacer->addMethodRepl($offset, $length, $name, $arguments, $body,
						$doxy, $modifiers, $returnType);
			}
			else
			{
				$type = '';
				$doxy = '';
				if ($doxyCode !== null)
				{
					$doxy = $this->parsePropertyDoxy($doxyCode, $type);
				}

				$replacer->addPropertyRepl($offset, $length, $name, $body, $doxy, $modifiers, $type);
			}
		}

		return $replacer->performReplace();
	}

	/**
	 * Parses body of class definition in object-oriented JavaScript source and
	 * transforms is to pseudo-C# code.
	 * See documented JavaScript sources for examples.
	 * @param string $code JavaScript code starting after opening parens of first
	 *		object literal with class member definitions
	 * @param string $className name of the class these members will belong to
	 * @param[out] string $overflow rest of the code after class terminator symbol
	 * @param int $flags
	 * @li @c CLASS_BODY_STATIC to make all members stacic
	 * @li @c CLASS_BODY_COMPOSITE to parse two consecutive object literals into
	 *		instance and static members respectively
	 * @return string transformed code
	 */
	public function parseClassBody ($code, $className, &$overflow,
			$flags = self::CLASS_BODY_NONE) {
		$overflow = $code;
		if (!preg_match('/^}(?:\);|(,\s*{))/m', $code, $matches, PREG_OFFSET_CAPTURE))
		{
			return '';
		}
		
		list($terminator, $separator) = $matches;
		$terminatorLength = strlen($terminator[0]);
		$afterTerminatorOffset = $this->getAfterOffset($terminator);
		$instanceMembers = substr($code, 0, $terminator[1]);

		$ret = $this->parseClassMembers($instanceMembers, $className,
				(bool)($flags & self::CLASS_BODY_STATIC));

		if ($separator[1] > -1) {
			$staticMembers = substr($code, $afterTerminatorOffset);
			if (!preg_match('/^}\);/m', $staticMembers, $matches1, PREG_OFFSET_CAPTURE))
			{
				return '';
			}

			list($finalTerminator) = $matches1;
			$afterFinalTerminatorOffset = $this->getAfterOffset($finalTerminator);
			$overflow = substr($code, $afterTerminatorOffset + $afterFinalTerminatorOffset);

			if ($flags & self::CLASS_BODY_COMPOSITE)
			{
				$staticMembers = substr($staticMembers, 0, $finalTerminator[1]);
				$ret .= $this->parseClassMembers($staticMembers, $className, true);
			}
		} else {
			$overflow = substr($code, $afterTerminatorOffset);
		}

		return $ret;
	}

}

?>