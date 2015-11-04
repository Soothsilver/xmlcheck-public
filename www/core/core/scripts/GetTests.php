<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets all tests manageable by user.
 * @n @b Requirements: one of following privileges: User::lecturesManageAll,
 *		User::lecturesManageOwn
 * @n @b Arguments: none
 */
final class GetTests extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::lecturesManageAll, User::lecturesManageOwn))
			return false;

		$tests = CommonQueries::GetTestsVisibleToUser();
		foreach ($tests as $test) {
			$this->addRowToOutput(
				[
					$test->getId(),
					$test->getDescription(),
					$test->getTemplate(),
					$test->getCount(),
					$test->getGenerated(),
					$test->getLecture()->getId(),
					$test->getLecture()->getName(),
					$test->getLecture()->getDescription()
				]
			);
		}
		return true;
	}
}

