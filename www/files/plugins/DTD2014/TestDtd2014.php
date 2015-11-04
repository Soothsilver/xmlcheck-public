<?php

use asm\utils\StringUtils,
	asm\plugin\CountedRequirements,
	asm\plugin\XmlRegex;
use asm\utils\Utils;
use Soothsilver\DtdParser;


/**
 * @ingroup plugins
 * Test for checking XML-DTD homework of XML Technologies lecture.
 * Accepted parameters are defined as class constants (and documented in-place).
 */
class TestDtd2014 extends \asm\plugin\XmlTest
{
	/// @name Names of accepted test parameters
	//@{
	const sourceXml = 'xml';	///< path of source XML file
	const sourceDtd = 'dtd';	///< path of source DTD file

	const dtdEmptyElements = 1;	///< minimum number of empty element definitions in DTD
	const dtdTextElements = 2;	///< minimum number of text element definitions in DTD
	const dtdModeledElements = 3;	///< minimum number of modeled element definitions in DTD
	/// minimum number of element occurrence limitations used in DTD
	const dtdAttributes = 5;	///< minimum number of attributes defined in DTD
	/// minimum number of attribute occurrence limitations used in DTD
	const dtdLimitedOccurrenceAttributes = 6;
	const dtdTextDataTypes = 7;	///< minimum number of text data types used in DTD
	const dtdEnumDataTypes = 8;	///< minimum number of enum data types used in DTD
	const dtdIdKeys = 9;	///< minimum number of ID keys used in DTD
	const dtdIdRefs = 10;	///< minimum number of ID references used in DTD
	const dtdOperators = 11;	///< minimum number of operators used in DTD
    const dtdEntities = 500;

	const xmlElements = 12;	///< minimum number of elements in XML
	const xmlAttributes = 13;	///< minimum number of attributes in XML
	const xmlKeys = 14;	///< minimum number of ID keys used in XML
	const xmlReferences = 15;	///< minimum number of ID references used in XML
	const xmlTextContent = 16;	///< minimum number of elements with text content in XML
	const xmlMixedContent = 17;	///< minimum number of elements with mixed content in XML
	const xmlDepth = 18;	///< minimum depth of XML document
	const xmlFanOut = 19;	///< minimum fan-out of XML document

	const specialEntities = 20;	///< minimum number of entities in XML or DTD
	const specialInstructions = 21;	///< minimum number of processing instructions in XML or DTD
	const specialCdata = 22;	///< minimum number of CDATA sections in XML or DTD
	const specialComments = 23;	///< minimum number of comments in XML or DTD
	//@}

	/// @name Goal IDs
	//@{
	const goalValidDtd = 'validDtd';	///< DTD is valid
	const goalWellFormedXml = 'wellFormedXml';	///< XML is well-formed
	const goalValidXml = 'validXml';	///< XML is valid
    const goalCorrectReferral = 'correctReferral';
	const goalCoveredDtd = 'coveredDtd';	///< DTD contains required constructs
	const goalCoveredXml = 'coveredXml';	///< XML contains required constructs
	const goalCoveredSpecials = 'coveredSpecials';	///< sources contain required special constructs
	//@}

    private $absolutePathToFolder;

	protected $xmlAttrIDs = array();		///< index of ID keys
	protected $xmlAttrIDRefs = array();	///< index of ID references

    public function __construct($absolutePathToFolder)
    {
        $this->absolutePathToFolder = $absolutePathToFolder;
    }

	protected function checkDTDValidity ($dtdString, $internalSubset, &$dtdDoc)
	{
        $dtdDoc = \Soothsilver\DtdParser\DTD::parseText($dtdString,$internalSubset);
        if ($dtdDoc->isWellFormedAndValid())
        {
            $this->reachGoal(self::goalValidDtd);
            return true;
        }
        else
        {
            $dtd_errors = "";
            foreach ($dtdDoc->errors as $error) {
                $dtd_errors .= "DTD well-formedness error: " . $error->getMessage() . "\n";
            }
            $this->failGoal(self::goalValidDtd, $dtd_errors);
            return false;
        }
	}
	protected function checkXMLValidity (\DOMDocument $xmlDom)
	{
		$this->useLibxmlErrors();
		$xmlDom->validate();
		return $this->reachGoalOnNoLibxmlErrors(self::goalValidXml, null);
	}

    /*************************************************************************************** THIS ***/
	/**
	 * Searches for occurrences of required DTD constructs and marks them in supplied
	 * index.
	 * @param \Soothsilver\DtdParser\DTD $dtdDoc source DTD
	 * @param CountedRequirements $reqs occurrence index
	 */
	protected function markDTDConstructOccurrences (\Soothsilver\DtdParser\DTD $dtdDoc, $reqs)
	{
		$operatorCounts = array(',' => 0, '|' => 0, '?' => 0, '+' => 0, '*' => 0);
		$operators = array_keys($operatorCounts);

        $reqs->addOccurrences(self::dtdEntities, count($dtdDoc->generalEntities));
        $reqs->addOccurrences(self::dtdEntities, count($dtdDoc->parameterEntities));

        foreach ($dtdDoc->elements as $element) {
            if ($element->contentSpecification === Soothsilver\DtdParser\Element::CONTENT_SPECIFICATION_EMPTY)
            { $reqs->addOccurrence(self::dtdEmptyElements);}
            else if ($element->contentSpecification === Soothsilver\DtdParser\Element::CONTENT_SPECIFICATION_ANY)
            { // We don't want to count ANY content model.
            }
            else if ($element->isPureText())
            { $reqs->addOccurrence(self::dtdTextElements);}
            else
            {
                // It has content model.
                $reqs->addOccurrence(self::dtdModeledElements);
                foreach ($operators as $operator)
                {
                    $operatorCounts[$operator] += substr_count($element->contentSpecification, $operator);
                }
            }
            foreach ($element->attributes as $attribute) {
                $reqs->addOccurrence(self::dtdAttributes);
                if ($attribute->defaultType === \Soothsilver\DtdParser\Attribute::DEFAULT_REQUIRED)
                {$reqs->addOccurrence(self::dtdLimitedOccurrenceAttributes); }
                if ($attribute->type === \Soothsilver\DtdParser\Attribute::ATTTYPE_ENUMERATION)
                {$reqs->addOccurrence(self::dtdEnumDataTypes);}
                if ($attribute->type === \Soothsilver\DtdParser\Attribute::ATTTYPE_CDATA)
                {$reqs->addOccurrence(self::dtdTextDataTypes);}
                if ($attribute->type === \Soothsilver\DtdParser\Attribute::ATTTYPE_ID)
                {
                    $reqs->addOccurrence(self::dtdIdKeys);
                    $this->xmlAttrIDs[] = "{$element->type}>{$attribute->name}";
                }
                if ($attribute->type === \Soothsilver\DtdParser\Attribute::ATTTYPE_IDREF)
                {
                    $reqs->addOccurrence(self::dtdIdRefs);
                    $this->xmlAttrIDRefs[] = "{$element->type}>{$attribute->name}";
                }
            }

        }
		sort($operatorCounts);
		$reqs->addOccurrences(self::dtdOperators, $operatorCounts[count($operatorCounts) - 1]);
	}

	/**
	 * Checks coverage of required DTD constructs.
	 * @param \Soothsilver\DtdParser\DTD $dtdDoc source DTD
	 * @return bool true on success
	 */
	protected function checkDTDConstructCoverage (\Soothsilver\DtdParser\DTD $dtdDoc)
	{
		$reqs = new CountedRequirements(array(
			'data' => array(
				self::dtdEmptyElements => 'empty elements',
				self::dtdTextElements => 'elements with text content',
				self::dtdModeledElements => 'elements with modeled content',
                self::dtdEntities => 'entities (general or parametric)',
				self::dtdAttributes => 'attributes',
				self::dtdLimitedOccurrenceAttributes => '#REQUIRED attributes',
				self::dtdTextDataTypes => 'text data types',
				self::dtdEnumDataTypes => 'enumerated data types',
				self::dtdIdKeys => 'id keys',
				self::dtdIdRefs => 'id references',
				self::dtdOperators => 'operators',
			),
			'counts' => array(),
		));
		$this->markDTDConstructOccurrences($dtdDoc, $reqs);
		return $this->resolveCountedRequirements($reqs, self::goalCoveredDtd);
	}

	/**
	 * Manages ID reference index.
	 * @param string $id ID value
	 * @param bool $isRef true if current entity is ID reference (otherwise it's
	 *		an ID key)
	 * @param array $ids ID reference index
	 * @return mixed true if value of @a $id has just been found both as ID key and
	 *		ID reference attribute value, false if it has been found just as one of
	 *		those so far, null if it @c true has been returned for this value already
	 */
	protected function manageIdReferences ($id, $isRef, &$ids)
	{
		if (!isset($ids[$id]))
		{
			$ids[$id] = $isRef;
			return false;
		}
		elseif ($ids[$id] !== null)
		{
			if ($ids[$id] != $isRef)
			{
				$ids[$id] = null;
				return true;
			}
		}
		return null;
	}

	/**
	 * Searches for required construct occurrences in XML and marks found occurrences
	 * in occurrence index.
	 * @param DOMElement $xmlEl source XML
	 * @param CountedRequirements $reqs occurrence index
	 * @param array $internal internal index of this method (<b>DO NOT use this
	 *		argument from other methods.</b>)
	 */
	protected function markXMLConstructOccurrences (
        DOMElement $xmlEl,
        $reqs,
		&$internal = array('elements' => array(), 'attributes' => array(), 'ids' => array(), 'level' => 0))
	{
        $elName = $xmlEl->nodeName;
		if (!isset($internal['elements'][$elName]))
		{
			$internal['elements'][$elName] = true;
			$reqs->addOccurrence(self::xmlElements);
		}

        $attributes = $xmlEl->attributes;
        for ($i = 0; $i < $attributes->length; $i++)
		{
            $attrName = $attributes->item($i)->nodeName;
			$internalAttrName = "$elName>$attrName";
			if (!isset($internal['attributes'][$internalAttrName]))
			{
				$internal['attributes'][$internalAttrName] = true;
				$reqs->addOccurrence(self::xmlAttributes);
			}
			
			$isRef = null;
			if (in_array($internalAttrName, $this->xmlAttrIDs))
			{
				$isRef = false;
			}
			if (in_array($internalAttrName, $this->xmlAttrIDRefs))
			{
				$isRef = true;
			}
			
			if ($isRef !== null)
			{
				if (($refUsed = $this->manageIdReferences((string)$attributes->item($i)->nodeValue, $isRef, $internal['ids'])) !== null)
				{
					$reqs->addOccurrence(self::xmlKeys);
					if ($refUsed) $reqs->addOccurrence(self::xmlReferences);
				}
			}
		}

        /**
         * @var DOMNodeList
         */
        $children = $xmlEl->childNodes;
		$level = $internal['level'];
		$reqs->tryRaiseCount(self::xmlDepth, $level);
		//$reqs->tryRaiseCount(self::xmlFanOut, $numChildren);
		$fanOut = 0;
        ++$level;
        $internal['level'] = $level;
        $hasText = false;
        $hasNodes = false;
        for ($i = 0; $i < $children->length; $i++)
        {
            $child = $children->item($i);
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                /** @var \DOMElement $child */
                $hasNodes = true;
                $fanOut++;
                $this->markXMLConstructOccurrences($child, $reqs, $internal);
            }
            else if ($child->nodeType == XML_TEXT_NODE)
            {
                if (trim($child->nodeValue) !== "")
                {
                    $hasText = true;
                }
            }
        }
        if ($hasText && !$hasNodes)
        {
            $reqs->addOccurrence(self::xmlTextContent);
        }
        if ($hasText && $hasNodes)
        {
            $reqs->addOccurrence(self::xmlMixedContent);
        }
        $reqs->tryRaiseCount(self::xmlFanOut, $fanOut);
	}

	/**
	 * Checks XML for occurrence of required constructs.
	 * @param DOMDocument $xml source XML
	 * @return bool true on success
	 */
	protected function checkXMLConstructCoverage (DOMDocument $xml)
	{
		$reqs = new CountedRequirements(array(
			'data' => array(
				self::xmlElements => 'used elements',
				self::xmlAttributes => 'used attributes',
				self::xmlKeys => 'used keys',
				self::xmlReferences => 'used key references',
				self::xmlTextContent => 'elements with text content',
				self::xmlMixedContent => 'elements with mixed content',
				self::xmlDepth => 'maximum depth of',
				self::xmlFanOut => 'maximum fan-out of',
			),
			'counts' => $this->params,
		));

        if ($xml->documentElement)
        {
            $this->markXMLConstructOccurrences($xml->documentElement, $reqs);
            return $this->resolveCountedRequirements($reqs, self::goalCoveredXml);
        }
        else {
            return $this->failGoal(self::goalCoveredXml,"XML does not contain a root element." );
        }
	}

	/**
	 * Checks XML and DTD documents for occurrences of required special constructs.
	 * @param string $xmlString source XML
	 * @param string $dtdString source DTD
	 * @return bool true on success
	 */
	protected function checkSpecialConstructCoverage ($xmlString, $dtdString)
	{
		$xmlRegex = XmlRegex::getInstance(XmlRegex::WRAP_PERL);
		$reqs = new CountedRequirements(array(
			'data' => array(
				self::specialEntities => array('entities', $xmlRegex->PEReference, $xmlRegex->Reference),
				self::specialInstructions => array('processing instructions', // $xmlRegex->PI), // < hangs PHP on preg_match (PHP bug)
						$xmlRegex->wrap('.{7}\<\?', null, XmlRegex::DOT_MATCH_ALL)),
				self::specialCdata => array('CDATA sections', $xmlRegex->CDSect),
				self::specialComments => array('comments', $xmlRegex->Comment),
			),
			'counts' => $this->params,
			'extras' => array('dtdRegex', 'xmlRegex'),
		));

		foreach ($reqs->getNames() as $name)
		{
			$matches = preg_match_all($reqs->getExtra('dtdRegex', $name), $dtdString, $dummy)
				+ preg_match_all($reqs->getExtra('xmlRegex', $name), $xmlString, $dummy);
			$reqs->addOccurrences($name, $matches);
		}
        $totalString = $xmlString . $dtdString;
        $generalEntityFound = false;
        $parameterEntityFound = preg_match($xmlRegex->PEReference, $totalString) > 0;
        // General entities.
        $success = preg_match_all("#&([^;]*);#u", $totalString, $generalEntityMatches, PREG_SET_ORDER);
        if ($success)
        {
            foreach($generalEntityMatches as $match)
            {
                if (!in_array($match[1], array("quot", "apos", "lt", "gt", "amp"), true))
                {
                    if (substr($match[1], 0, 1) === "#")
                    {
                       // Character reference entities do not count.
                    }
                    else
                    {
                        $generalEntityFound = true;
                    }
                } // Pre-defined entities do not count.
            }
        }
        if (!$generalEntityFound && !$parameterEntityFound)
        {
            return $this->failGoal(self::goalCoveredSpecials, "You did not use a general or parameter entity. The predefined entities 'quot', 'apos', 'amp', 'lt' and 'gt' do not count for this purpose. Character reference entities also do not count.");
        }
        // Rest.
        return $this->resolveCountedRequirements($reqs, self::goalCoveredSpecials);
	}

	protected function main ()
	{
        // Load the two files
        $xmlFile = '';
        $dtdFile = '';
        $this->loadFiles($this->absolutePathToFolder, $xmlFile, $dtdFile); // may add errors to the error list
        if ($this->hasErrors())
        {
           return;
        }

        // Files loaded.
        $this->addGoals(array(
            self::goalWellFormedXml => 'XML is well-formed',
            self::goalValidXml => 'XML is valid to supplied DTD',
            self::goalValidDtd => 'DTD is valid',
            self::goalCorrectReferral => 'The XML\'s DOCTYPE declaration refers to the provided DTD.',
            self::goalCoveredDtd => 'DTD document contains required constructs',
            self::goalCoveredXml => 'XML document contains required constructs',
            self::goalCoveredSpecials => 'Documents contain required special constructs',
        ));

	    StringUtils::removeBomFromFile($dtdFile);
		$dtdString = file_get_contents($dtdFile);
        $xmlString = file_get_contents($xmlFile);
        $dtdString = $this->convertToUtf8($dtdString);
        $xmlString = $this->convertToUtf8($xmlString);
        if ($xmlString === false)
        {
            $this->addError("The XML file must be either in UTF-8 or in UTF-16.");
        }
        if ($dtdString === false)
        {
            $this->addError("The DTD file must be either in UTF-8 or in UTF-16.");
        }
        if ($this->hasErrors())
        {
            return;
        }
		if ($this->loadXml($xmlFile, true, $xmlDom, $error))
		{
            $xmlDom->formatOutput = true;
            /**
             * @var DOMDocument $xmlDomDocument
             */
            $xmlDomDocument = $xmlDom;
            if (!$xmlDomDocument->doctype)
            {
                $this->failGoal(self::goalCorrectReferral, "The XML does not have a DOCTYPE declaration.");
                if ($this->checkDTDValidity($dtdString, "", $dtdDoc))
                {
                    $this->checkDTDConstructCoverage($dtdDoc);
                }
                else
                {
                    $this->failGoal(self::goalCoveredDtd, 'Document did not pass DTD well-formedness and validity checks.');
                }
            }
            else
            {
                if ($xmlDomDocument->doctype->systemId === basename($dtdFile) || $xmlDomDocument->doctype->systemId === "./" . basename($dtdFile))
                {
                    $this->reachGoal(self::goalCorrectReferral);
                }
                else
                {
                    $this->failGoal(self::goalCorrectReferral, "The XML file refers to a different DTD than the one provided.");
                }

                $internalSubset = $xmlDomDocument->doctype->internalSubset;

                if ($this->checkDTDValidity($dtdString, $internalSubset, $dtdDoc))
                {
                    $this->checkDTDConstructCoverage($dtdDoc);
                }
                else
                {
                    $this->failGoal(self::goalCoveredDtd, 'Document did not pass DTD well-formedness and validity checks.');
                }
            }
            $this->reachGoal(self::goalWellFormedXml);
			$this->checkXMLValidity($xmlDom);
			$this->checkXMLConstructCoverage($xmlDom);
			$this->checkSpecialConstructCoverage($xmlString, $dtdString);
		}
		else
		{
            if ($this->checkDTDValidity($dtdString, "", $dtdDoc))
            {
                $this->checkDTDConstructCoverage($dtdDoc);
            }
            else
            {
                $this->failGoal(self::goalCoveredDtd, 'Document did not pass DTD well-formedness and validity checks.');
            }
			$this->failGoals(array(self::goalWellFormedXml, self::goalValidXml,
					self::goalCoveredXml, self::goalCoveredSpecials, self::goalCorrectReferral), $error);
		}
	}

    /**
     * Attempts to find an XML and a DTD filename in the given folder and adds an error if it cannot find them.
     * @param $fromWhere string the directory to load the files from
     * @param $xmlFile string The found XML filename.
     * @param $dtdFile string The found DTD filename.
     */
    private function loadFiles($fromWhere, &$xmlFile, &$dtdFile)
    {
        $xmlFile = false;
        $dtdFile = false;
        $files = \asm\utils\Filesystem::getFiles($fromWhere);
        foreach ($files as $file)
        {
            if (Utils::endsWith(strtolower($file), ".xml"))
            {
                if ($xmlFile === false)
                {
                    $xmlFile = \asm\utils\Filesystem::combinePaths($fromWhere, $file);
                }
                else
                {
                    $this->addError("There are two or more .xml files in your submission. There must only be one.");
                }
            }
            if (Utils::endsWith(strtolower($file), ".dtd"))
            {
                if ($dtdFile === false)
                {
                    $dtdFile = \asm\utils\Filesystem::combinePaths($fromWhere, $file);
                }
                else
                {
                    $this->addError("There are two or more .dtd files in your submission. There must only be one.");
                }
            }
        }

        if ($xmlFile === false)
        {
            $this->addError("Your submission must contain an XML file ending with '.xml'.");
        }
        if ($dtdFile === false)
        {
            $this->addError("Your submission must contain a DTD file ending with '.dtd'.");
        }
    }
}

