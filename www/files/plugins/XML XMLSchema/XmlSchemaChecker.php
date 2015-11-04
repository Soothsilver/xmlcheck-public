<?php

require_once 'TestXmlSchema.php';

/**
 * @ingroup plugins
 * @projectname plugin for checking XMLSchema homework of XML Technologies lecture.
 * Requires solutions to contain source files @c "data.xml" and @c "data.xsd".
 * Uses TestXmlSchema to perform checking.
 */
class XmlSchemaChecker extends \asm\plugin\SingleTestPlugin
{
	/**
	 * @copybrief asm::plugin::SingleTestPlugin::setUp()
	 *
	 * @param array $params currently doesn't use any arguments
	 */
	protected function setUp ($params)
	{
		$this->setTest(new TestXmlSchema($this->dataFolder),
            array()
        );
	}
}

