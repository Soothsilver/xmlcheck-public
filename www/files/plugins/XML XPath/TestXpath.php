<?php

use asm\plugin\CountedRequirements,
	asm\plugin\XmlRegex,
	asm\utils\Filesystem,
	asm\utils\StringUtils;

/**
 * @ingroup plugins
 * Test for checking XPath homework of XML Technologies lecture.
 * Accepted parameters are defined as class constants (and documented in-place).
 */
class TestXpath extends \asm\plugin\XmlTest
{
	/// @name Names of accepted test parameters
	//@{
	const sourceXml = 'xml';		///< path to source XML file
	/// path template of source XPath files (must contain single @c %d placeholder)
	const sourceXpath = 'xpath';

	/// filename template of output files with results of evaluated XPath expressions
	const outputXpathMask = 'xpathOutputMask';

	const expressions = 1;	///< minimum number of supplied XPath expressions
	const attributeTests = 2;	///< minimum number of attribute tests
	const descendantExistenceTests = 3;	///< minimum number of descendant existence tests
	const descendantNonExistenceTests = 4;	///< minimum number of descendant non-existence tests
	const positionTests = 5;	///< minimum number of position tests
	const countTests = 6;	///< minimum number of count tests
	const axes = 7;	///< minimum number of axes
	//@}

	/// @name Goal IDs
	//@{
	const goalMinExpressionCount = 'minExpressionCount';	///< enough XPath expressions supplied
	const goalCoveredXpath = 'coveredXpath';	///< XPath expressions contain all required constructs
	const goalValidXpath = 'validXpath';	///< XPath expressions are valid
	//@}

	/**
	 * Loads XPath expressions from source files.
	 * @param string $template source files path template (must contain single
	 *		@c %d placeholder)
	 * @param[out] array $expressions found XPath expressions
	 * @param[out] array $comments found XPath comments in source files
	 * @return bool true on success
	 */
	protected function loadExpressions ($template, &$expressions, &$comments)
	{
		$expressions = array();
		for ($i = 1; true; ++$i)
		{
			$filename = sprintf($template, $i);
			if (!file_exists($filename))
			{
				break;
			}

			$expr = $this->stripXpathComments(file_get_contents($filename), $comment);
			$expressions[] = trim($expr);
			$comments[] = trim($comment);
		}
		
		$minCount = isset($this->params[self::expressions]) ? $this->params[self::expressions] : 1;
		return $this->reachGoalOnCondition((count($expressions) >= $minCount), self::goalMinExpressionCount,
				"Not enough XPath expression files supplied (at least $minCount required)");
	}

	/**
	 * Strips XPath comments from supplied XPath expression string.
	 * @param string $expr XPath expression
	 * @param[out] array $comments stripped comments
	 * @return string expression @a $expr stripped of comments
	 */
	protected function stripXpathComments ($expr, &$comments)
	{
		$com = array();
		$expr = StringUtils::stripComments($expr, '{--', '--}', $com);
		$expr = StringUtils::stripComments($expr, '(:', ':)', $com);
		$comments = implode("\n", $com);
		return $expr;
	}

	/**
	 * Checks coverage of required constructs in supplied XPath expressions.
	 * @param array $expressions
	 */
	protected function checkXPathConstructsCoverage ($expressions)
	{
		$xmlRegex = XmlRegex::getInstance(XmlRegex::WRAP_NONE);
		$xmlNameRegex = $xmlRegex->Name;
		$xmlRegex->setWrapMode(XmlRegex::WRAP_PERL);

		$comparisonOp = '([<>]|[!<>]?=)';
		$name = "($xmlNameRegex|\\*)";
		$descendantIdent = "(((child|descendant)::)?$name|node\\(\\))";
		$descendantSeq = "(\\./|//)?($descendantIdent//?)*$descendantIdent";
		$attributeIdent = "(@|attribute::)$name";
		$condOp = '\s+(and|or)\s+';
		$condOpen = "(\\[|$condOp)";
        $optionalWhitespace = '\s*';
		$condClose = "(\\]|\\[|$condOp)"; // Includes opening brace because there might be additional predicate

		$reqs = new CountedRequirements(array(
			'data' => array(
                // We are somewhat lenient. This will allow attributes even in main path.
				self::attributeTests => array('attribute value tests',
					$xmlRegex->wrap("{$attributeIdent}")),
				self::descendantExistenceTests => array('descendant existence tests',
					$xmlRegex->wrap("{$condOpen}{$optionalWhitespace}{$descendantSeq}{$optionalWhitespace}{$condClose}")),
				self::descendantNonExistenceTests => array('descendant non-existence tests',
					$xmlRegex->wrap("{$condOpen}{$optionalWhitespace}not{$optionalWhitespace}\\({$optionalWhitespace}$descendantSeq{$optionalWhitespace}\\){$optionalWhitespace}{$condClose}")),
				self::positionTests => array('position tests',
					$xmlRegex->wrap("({$optionalWhitespace}position{$optionalWhitespace}\\({$optionalWhitespace}\\){$optionalWhitespace}$comparisonOp)|({$comparisonOp}{$optionalWhitespace}position{$optionalWhitespace}\\({$optionalWhitespace}\\))|(last{$optionalWhitespace}\\({$optionalWhitespace}\\))|(\\[{$optionalWhitespace}[0-9]+{$optionalWhitespace}\\])")),
				self::countTests => array('count tests',
					$xmlRegex->wrap("count{$optionalWhitespace}\\(")),
				self::axes => array('axes',
					$xmlRegex->wrap('(ancestor|ancestor-or-self|attribute|child|descendant'
							. '|descendant-or-self|following|following-sibling|namespace'
							. '|parent|preceding|preceding-sibling|self)::')),
			),
			'counts' => $this->params,
			'extras' => array('xpathRegex'),
		));

		foreach ($reqs->getNames() as $name)
		{
			foreach ($expressions as $expr)
			{
				$matches = preg_match_all($reqs->getExtra('xpathRegex', $name), $expr, $dummy);
				$reqs->addOccurrences($name, $matches);
			}
		}
		
		$this->resolveCountedRequirements($reqs, self::goalCoveredXpath);
	}

	/**
	 * Evaluates XPath expressions on XML and outputs results to files.
	 * @param DOMDocument $xmlDom source XML
	 * @param array $expressions XPath expressions
	 * @param array $comments XPath comments belonging to @a $expressions
	 * @return bool true on success
	 */
	protected function evaluateXpathExpressions ($xmlDom, $expressions, $comments)
	{
		$evaluator = new DOMXpath($xmlDom);
		$exprNumber = 0;

		for ($i = 0; $i < count($expressions); ++$i)
		{
			$expr = $expressions[$i];

			$this->useLibxmlErrors();
			$result = $evaluator->query($expr);
			$errors = $this->getLibxmlErrors();

			if ($errors)
			{
				return $this->failGoal(self::goalValidXpath, "XPath expression is invalid ($expr).");
			}

			$exprComments = (!isset($comments[$i]) || empty($comments[$i])) ? '' : <<<COMMENTS
#	Comments from expression file:

{$comments[$i]}

COMMENTS;
			$output = <<<HEADER
#	Results for XPath expression: $expr
$exprComments
#	Found {$result->length} matching DOM nodes.
HEADER;
			$delimiter = "\n\n#	%s result:\n\n";
			for ($j = 0; $j < $result->length; ++$j)
			{
				$output .= sprintf($delimiter, "Result " . ($j + 1))
						. $xmlDom->saveXML($result->item($j));
			}

			Filesystem::stringToFile(sprintf($this->params[self::outputXpathMask], ++$exprNumber), $output);
		}

		return $this->reachGoal(self::goalValidXpath);
	}

	protected function main ()
	{
		$this->addGoal(self::goalMinExpressionCount,
				'Required minimum of XPath expression was supplied');
		$this->addGoal(self::goalCoveredXpath, 'XPath expressions contain all required constructs');
		$this->addGoal(self::goalValidXpath, 'Supplied expressions are valid XPath');

		if (!$this->checkSources(self::sourceXml))
        {
            $this->addError("Explanation: The uploaded ZIP file is missing the 'data.xml' file. " .
            "This file must be present and must be named this way.");
			return;
        }
		$this->loadExpressions($this->paths[self::sourceXpath], $expressions, $comments);
		$this->checkXPathConstructsCoverage($expressions);

		if ($this->loadXml($this->paths[self::sourceXml], false, $xmlDom, $error))
		{
			$this->evaluateXpathExpressions($xmlDom, $expressions, $comments);
		}
		else
		{
			$this->failGoal(self::goalValidXpath, $error);
		}
	}
}

