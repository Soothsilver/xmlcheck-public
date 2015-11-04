<?php

use asm\utils\Filesystem,
	asm\utils\Utils;

/**
 * @ingroup plugins
 * Test for checking XSLT homework of XML Technologies lecture.
 * Accepted parameters are defined as class constants (and documented in-place).
 */
class TestXslt extends \asm\plugin\XmlTest
{
	/// @name Names of accepted test parameters
	//@{

	/// filename base (without suffix) of output file with transformed XML
	const outputTransformedXmlBase = 'xmlOutputBase';
	/// Filename template of output files with XPath expressions used in XSLT.
	/// @note Must contain one @c %d placeholder for file index
	const outputUsedXpathMask = 'xpathOutputMask';

	const templates = 1;	///< minimum number of template definitions
	const matchTemplates = 2;	///< minimum number of match template definitions
	const namedTemplates = 3;	///< minimum number of named template definitions
	const templateCalls = 4;	///< minimum number of template calls
	const templateCallModes = 5;	///< minimum number of template call modes
	const conditionals = 6;	///< minimum number of conditional expressions
	const cycles = 7;	///< minimum number of cycles
	const variables = 8;	///< minimum number of variables
	const params = 9;	///< minimum number of parameters
	const outputCommands = 10;	///< minimum number of output commands
	const copyCommands = 11;	///< minimum number of copy commands
	//@}

	/// @name Goal IDs
	//@{
	const goalValidXslt = 'validXslt';	///< XSLT is valid
	const goalCoveredXslt = 'coveredXslt';	///< XSLT contains all required constructs
	//@}
    private $absolutePathToFolder;
    public function __construct($absolutePathToFolder)
    {
        $this->absolutePathToFolder = $absolutePathToFolder;
    }

	//protected $xslNamespace = 'http://www.w3.org/1999/XSL/Transform';

	/**
	 * Transforms XML document using XSLT.
	 * @param DOMDocument $xmlDom source XML
	 * @param DOMDocument $xsltDom source XSLT
	 * @param string $outputFilenameBase base of output file with transformed XML
	 *		to be created
	 * @return bool true on success
	 */
	protected function checkXsltValidity(DOMDocument $xmlDom, DOMDocument $xsltDom,
			$outputFilenameBase)
	{
		$this->useLibxmlErrors();
		$proc = new XSLTProcessor();
		$proc->importStylesheet($xsltDom);
		if (!$this->failGoalOnLibxmlErrors(self::goalValidXslt, null))
		{
			return false;
		}

		$this->useLibxmlErrors();
		$newXmlDom = $proc->transformToDoc($xmlDom);
		if (!$this->reachGoalOnNoLibxmlErrors(self::goalValidXslt, null))
		{
			return false;
		}

		$output = $newXmlDom->saveHTML();
		$suffix = '.html';
		if (!$output)
		{
			$output = $newXmlDom->saveXML();
			$suffix = '.xml';
		}
		Filesystem::stringToFile($outputFilenameBase . $suffix, $output);

		return true;
	}

	/**
	 * Checks coverage of required constructs in XSLT.
	 * Minima for individual construct occurrences may be supplied as test parameters
	 * on test run.
	 * @param SimpleXMLElement $xsltXml source XSLT
	 * @return bool true on success
	 */
	protected function checkXsltConstructCoverage(SimpleXMLElement $xsltXml)
	{
        $xsltXml->registerXPathNamespace("xsl", "http://www.w3.org/1999/XSL/Transform");
		return $this->checkUsingXpath(
			$xsltXml,
			self::goalCoveredXslt,
			array(
				'data' => array(
					self::templates => array('used templates', '/xsl:stylesheet/xsl:template'),
					self::matchTemplates => array('used match templates',
						'/xsl:stylesheet/xsl:template[@match]'),
					self::namedTemplates => array('used named templates',
						'/xsl:stylesheet/xsl:template[@name]'),
					self::templateCalls => array('used calls of unique templates',
						'//xsl:call-template[@name=/xsl:stylesheet/xsl:template/@name] | //xsl:apply-templates'),
					self::templateCallModes => array('used template call modes',
						'//xsl:apply-templates[@mode=/xsl:stylesheet/xsl:template/@mode]'),
					self::conditionals => array('used conditional statements',
						'//xsl:if | //xsl:choose[./xsl:when]'),
					self::cycles => array('used cycles', '//xsl:for-each'),
					self::variables => array('used variables', '//xsl:variable'),
					self::params => array('parameter uses',
						'/xsl:stylesheet/xsl:template/xsl:param[@name=/*//xsl:with-param/@name]'),
					self::outputCommands => array('used output-creating statements',
						'//xsl:element | //xsl:attribute | //xsl:value-of | //xsl:text'),
					self::copyCommands => array('used copy statements', '//xsl:copy | //xsl:copy-of'),
				),
				'counts' => $this->params,
			)
		);
	}

	protected function main()
	{
		$this->addGoal(self::goalValidXslt, 'XML and XSLT are valid');
		$this->addGoal(self::goalCoveredXslt, 'XSLT document contains all required constructs');

        $this->loadFiles($this->absolutePathToFolder, $xmlFile, $xslFile);

		if ($this->hasErrors())
        {
            return;
        }

		if ($this->loadXml($xslFile, false, $xsltDom, $error))
		{
			$this->checkXsltConstructCoverage(simplexml_import_dom($xsltDom));

			if ($this->loadXml($xmlFile, false, $xmlDom, $error))
			{
			    $this->checkXsltValidity($xmlDom, $xsltDom, $this->params[self::outputTransformedXmlBase]);
			}
			else
			{
                $this->failGoal(self::goalValidXslt, $error);
			}
		}
		else
		{
			$this->failGoal(self::goalValidXslt, $error);
			$this->failGoal(self::goalCoveredXslt, $error);
		}
	}

	/**
	 * Attempts to find an XML and an XSL filename in the given folder and adds an error if it cannot find them.
	 * @param $fromWhere string directory from where to load the files
	 * @param $xmlFile string The found XML filename.
	 * @param $xslFile string The found XSL filename.
	 */
    private function loadFiles($fromWhere, &$xmlFile, &$xslFile)
    {
        $xmlFile = false;
        $xslFile = false;
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
            if (Utils::endsWith(strtolower($file), ".xsl"))
            {
                if ($xslFile === false)
                {
                    $xslFile = \asm\utils\Filesystem::combinePaths($fromWhere, $file);
                }
                else
                {
                    $this->addError("There are two or more .xsl files in your submission. There must only be one.");
                }
            }
        }

        if ($xmlFile === false)
        {
            $this->addError("Your submission must contain an XML file ending with '.xml'.");
        }
        if ($xslFile === false)
        {
            $this->addError("Your submission must contain an XSL file ending with '.xsl'.");
        }
    }
}

