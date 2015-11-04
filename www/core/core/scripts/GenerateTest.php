<?php

namespace asm\core;


/**
 * @ingroup requests
 * Generates a test from saved template.
 * @n @b Requirements: either User::lecturesManageAll privilege, or User::lecturesManageOwn
 *		privilege and be the owner of the lecture this test belongs to
 * @n @b Arguments:
 * @li @c id test ID
 */
final class GenerateTest extends GenTestScript
{
	protected function body ()
	{
		if (!$this->isInputValid(array('id' => 'isIndex')))
			return false;

		$id = $this->getParams('id');

		/** @var \XTest $test */
		$test = Repositories::findEntity(Repositories::Xtest, $id);

		if (!$this->checkTestGenerationPrivileges($test->getLecture()->getId()))
			return false;

		$randomized = $this->generateTest($test->getTemplate(), $test->getCount());

		$test->setGenerated(implode(',', $randomized));
		Repositories::persistAndFlush($test);
		return true;
	}
}

