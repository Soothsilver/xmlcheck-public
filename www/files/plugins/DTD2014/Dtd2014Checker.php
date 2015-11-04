<?php

require_once __DIR__ . '/TestDtd2014.php';

class Dtd2014Checker extends \asm\plugin\SingleTestPlugin
{
	/**
	 * @copybrief asm::plugin::SingleTestPlugin::setUp()
	 *
	 * @param array $params uses following arguments:
	 * @li minimum XML depth
	 * @li minimum XML fan-out
	 */
	protected function setUp ($params)
	{
		$this->setTest(new TestDtd2014($this->dataFolder), array(),
            array(
				TestDtd2014::xmlDepth => isset($params[0]) ? $params[0] : 0,
                TestDtd2014::xmlFanOut => isset($params[1]) ? $params[1] : 0,
			));
	}
}

