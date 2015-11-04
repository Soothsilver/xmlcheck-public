<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets
 */
final class GetTeacherRatings extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::groupsManageAll, User::groupsManageOwn))
			return false;
		$canManageAll = User::instance()->hasPrivileges(User::groupsManageAll);
		/*
		 * Output format documentation:
		 * GROUPID => [
		 * 	name
		 *  lecture (name)
		 *  owned (true/false, whether owned by the active user)
		 *  assignments
		 *   ASSIGNMENTID => [
		 *    problem (name)
		 *    reward (maximum reward int)
		 *   ]
		 *  students
		 *   STUDENTID => [
		 *    name (RealName)
		 *    ratings
		 *     ASSIGNMENTID => [
		 *      id (SubmissionId)
		 *      rating (submission rating)
		 *     ]
		 *    sum (sum of all ratings for all submissions of this student for this group)
		 *   ]
		 * ]
		 */
		/** @var \Submission[] $submissions */
		$submissions = Repositories::getEntityManager()->createQuery('SELECT s, a, g, p, l FROM \Submission s JOIN s.assignment a JOIN a.group g JOIN g.lecture l JOIN a.problem p WHERE s.status <> \'deleted\' AND a.deleted = false AND g.deleted = false AND l.deleted = false AND p.deleted = false')->getResult();
		$result = array();
		foreach ($submissions as $s)
		{

			$assignment = $s->getAssignment();
			$group = $assignment->getGroup();
			$lecture = $group->getLecture();
			$problem = $assignment->getProblem();
			if (!$canManageAll && $group->getOwner()->getId() !== User::instance()->getId()) {
				continue;
			}
			if (!isset($result[$group->getId()]))
			{
				$result[$group->getId()] = array(
					'name' => $group->getName(),
					'lecture' => $lecture->getName(),
					'owned' => ($group->getOwner()->getId() === User::instance()->getId()),
					'assignments' => array(),
					'students' => array()
				);
			}

			$assignments = & $result[$group->getId()]['assignments'];
			if (!isset($assignments[$assignment->getId()]))
			{
				$assignments[$assignment->getId()] = array(
					'problem' => $problem->getName(),
					'reward' => $assignment->getReward()
				);
			}

			$students = & $result[$group->getId()]['students'];
			if (!isset($students[$s->getUser()->getId()]))
			{
				$students[$s->getUser()->getId()] = array(
					'name' => $s->getUser()->getRealName(),
					'ratings' => array(),
					'sum' => 0,
				);
			}

			$student =& $students[$s->getUser()->getId()];
			$ratings =& $student['ratings'];
			$ratings[$assignment->getId()] = array(
				'id' => $s->getId(),
				'rating' => $s->getRating(),
			);

			if ($s->getStatus() === \Submission::STATUS_GRADED && is_numeric($s->getRating())) {
				$student['sum'] += $s->getRating();
			}
		}

		$this->setOutput($result);
		return true;
	}
}

