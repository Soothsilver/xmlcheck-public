<?php



require_once 'TestXpath.php';

/**
 * @ingroup plugins
 * @projectname plugin for checking XPath homework of XML Technologies lecture.
 * Requires solutions to contain source files @c "data.xml" and @c "xpath/xpath&lt;N&gt;.xp",
 * where &lt;N&gt; starts at one.
 * Uses TestXpath to perform checking.
 * Outputs multiple files @c "xpathEvalResult&lt;N&gt;.txt", one for each supplied XPath
 * expression.
 */
class XpathChecker extends \asm\plugin\SingleTestPlugin
{
	/**
	 * @copybrief asm::plugin::SingleTestPlugin::setUp()
	 *
	 * @param array $params uses following arguments:
	 *	@li @optional (int) minimum of required XPath expressions (defaults to 5)
	 */
	protected function setUp ($params)
	{
		$this->setTest(new TestXpath(), array(
				TestXpath::sourceXml => 'data.xml',
				TestXpath::sourceXpath => 'xpath/xpath%d.xp',
			),	array(
				TestXpath::outputXpathMask => $this->getOutputFile('xpathEvalResult%d.txt'),
				TestXpath::expressions => isset($params[0]) ? $params[0] : 5,
			));
	}
}

