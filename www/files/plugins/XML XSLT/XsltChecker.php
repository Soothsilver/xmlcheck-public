<?php

require_once 'TestXslt.php';

/**
 * @ingroup plugins
 * @projectname plugin for checking XSLT homework of XML Technologies lecture.
 * Requires solutions to contain source files @c "data.xml" and @c "data.xsl".
 * Uses TestXslt to perform checking.
 * Outputs one file called @c "dataXslTransformed.(html|xml)" (suffix depends on
 * output type specified on @c "data.xsl") and multiple files @c "xpath/xsltXpath&lt;N&gt;.xp",
 * where &lt;N&gt; starts at zero, with XPath expressions used in source XSLT.
 */
class XsltChecker extends \asm\plugin\SingleTestPlugin
{
	/**
	 * @copybrief asm::plugin::SingleTestPlugin::setUp()
	 * 
	 * @param array $params uses following arguments:
	 *	@li @optional (int) minimum of XSLT template definitions & calls required
	 *		(defaults to 5)
	 */
	protected function setUp ($params)
	{
		$templates = isset($params[0]) ? $params[0] : 5;

		$this->setTest(new TestXslt($this->dataFolder), array(),
            array(
				TestXslt::outputTransformedXmlBase => $this->getOutputFile('dataXslTransformed'),
				TestXslt::templates => $templates,
				TestXslt::templateCalls => $templates,
			));
	}
}

