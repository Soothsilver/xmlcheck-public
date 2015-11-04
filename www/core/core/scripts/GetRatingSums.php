<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets user's rating sums for subscribed groups.
 * @n @b Requirements: User::assignmentsSubmit
 * @n @b Arguments: none
 */
final class GetRatingSums extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::assignmentsSubmit))
			return false;

		$ratings = Repositories::getEntityManager()->createQuery(
			"SELECT s, SUM(s.rating) AS rating, a, g.id FROM \Submission s JOIN s.assignment a JOIN a.group g WHERE s.user = :userId AND s.status = 'graded' GROUP BY g, g.name"
		)->setParameter('userId', User::instance()->getId())->getResult();

		foreach ($ratings as $rating) {
			/** @var \Group $group */
			$group = Repositories::findEntity(Repositories::Group, (integer)$rating["id"]);
			$this->addRowToOutput([
				User::instance()->getName(),
				User::instance()->getEmail(),
				$rating["id"],
				$rating["rating"],
				$group->getName(),
				$group->getDescription(),
				$group->getLecture()->getName(),
				$group->getLecture()->getDescription()
			]);
		}

		return true;
	}
}

