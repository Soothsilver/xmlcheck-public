<?php

namespace asm\core;

/**
 * @ingroup requests
 * Checks whether supplied name is taken for supplied subject type.
 * @n @b Requirements: none
 * @n @b Arguments:
 * @li @c table subject type (possible values: @c groups, @c lectures, @c plugins
 *		@c problems, @c users, @c usertypes)
 */
final class IsNameTaken extends DataScript
{
	protected function body ()
	{
		if (!$this->isInputSet(array('name', 'table')))
			return false;

		$table = $this->getParams('table');
		$name = strtolower($this->getParams('name'));
		$columnName = "name";
		switch ($table)
		{
			case 'groups':
				$repositoryName = Repositories::Group;
				break;
			case 'lectures':
				$repositoryName = Repositories::Lecture;
				break;
			case 'plugins':
				$repositoryName = Repositories::Plugin;
				break;
			case 'problems':
				$repositoryName = Repositories::Problem;
				break;
			case 'users':
				$repositoryName = Repositories::User;
				break;
			case 'usertypes':
				$repositoryName = Repositories::UserType;
				break;
			default:
				return $this->stop('name duplicity check cannot be performed on table' . $table);
		}

		$repository = Repositories::getRepository($repositoryName);
		$results = $repository->findBy([$columnName => $name]);
		$nameConflict = (count($results) > 0);
		if ($table == 'groups' || $table == 'lectures' || $table == 'problems') {
			$nameConflict = false;
			// For these three, we now allow duplicate names.
		}
		$this->addOutput('nameTaken', $nameConflict);
		return true;
	}
}

