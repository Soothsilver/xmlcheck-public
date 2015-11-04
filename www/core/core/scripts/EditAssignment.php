<?php

namespace asm\core;
use asm\core\lang\Language;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Creates or edits assignment.
 * @n @b Requirements: either User::groupsManageAll privilege, or User::groupsManageOwn
 *		privilege and be the owner of the group the assignment belongs to
 * @n @b Arguments:
 * @li @c id @optional assignment ID (required for edit)
 * @li @c group @optional group ID (required for add, cannot be edited)
 * @li @c problem @optional problem ID (required for add, cannot be edited)
 * @li @c deadline assignment deadline date
 * @li @c reward maximum submission rating
 */
final class EditAssignment extends DataScript
{
	protected function body ()
	{
        // Validate input.
		$inputs = array(
			'group' => 'isIndex',
			'problem' => 'isIndex',
			'deadline' => 'isDate',
			'reward' => 'isNonNegativeInt',
		);
		if (!$this->isInputValid($inputs))
        {
			return false;
        }

        // Load input
        $id = $this->getParams('id');
        $group = $this->getParams('group');
        $problem = $this->getParams('problem');
        $deadline = $this->getParams('deadline');
        $reward = $this->getParams('reward');

        // Adjust input
		$deadline = $deadline . ' 23:59:59';

        // Load from database
        /**
         * @var $groupEntity \Group
         * @var $assignmentEntity \Assignment
         * @var $problemEntity \Problem
         */
        $groupEntity = Repositories::getEntityManager()->find('Group', $group);
        $problemEntity = Repositories::getEntityManager()->find('Problem', $problem);
        if ($groupEntity === null || $problemEntity === null)
        {
           return $this->stop('Group or problem does not exist.', 'Assignment cannot be edited.');
        }

        // Authenticate
        $user = User::instance();
        if (!$user->hasPrivileges(User::groupsManageAll)
            && (!$user->hasPrivileges(User::groupsManageOwn) || ($groupEntity->getOwner()->getId() !== $user->getId()))
           )
            return $this->stop(Language::get(StringID::InsufficientPrivileges));

        // Already exists?
        if ($id !== null && $id !== '')
        {
            $assignmentEntity = Repositories::getEntityManager()->find('Assignment', $id);
            $assignmentEntity->setDeadline(\DateTime::createFromFormat("Y-m-d H:i:s", $deadline));
            $assignmentEntity->setReward($reward);
            Repositories::getEntityManager()->persist($assignmentEntity);
            Repositories::getEntityManager()->flush($assignmentEntity);
        }
        else
        {
            // Verify integrity
            if ($problemEntity->getLecture()->getId() !== $groupEntity->getLecture()->getId())
            {
                return $this->stop('You are adding an assignment for problem belonging to lecture X to a group that belongs to lecture Y. This is not possible.');
            }

            // Create new

            $assignmentEntity = new \Assignment();
            $assignmentEntity->setGroup($groupEntity);
            $assignmentEntity->setProblem($problemEntity);
            $assignmentEntity->setDeadline(\DateTime::createFromFormat("Y-m-d H:i:s", $deadline));
            $assignmentEntity->setReward($reward);
            Repositories::getEntityManager()->persist($assignmentEntity);
            Repositories::getEntityManager()->flush($assignmentEntity);

            // Send e-mail
            /**
             * @var $subscription \Subscription
             */
            $query =  Repositories::getEntityManager()->createQuery('SELECT s, u FROM Subscription s JOIN s.user u  WHERE s.group = :group');
            $query->setParameter('group', $groupEntity);
            $subscriptions = $query->getResult();
            foreach($subscriptions as $subscription)
            {
                if (!$subscription->getUser()->getSendEmailOnNewAssignment()) { continue; }

                $to = $subscription->getUser()->getEmail();
                $email = file_get_contents(Config::get("paths", "newAssignmentEmail"));
                $email = str_replace( "%{Problem}", $problemEntity->getName(), $email);
                $email = str_replace( "%{Deadline}", $deadline, $email);
                $email = str_replace( "%{Group}", $groupEntity->getName(), $email);
                $email = str_replace( "%{Link}", Config::getHttpRoot() . "#studentAssignments#" . $assignmentEntity->getId(), $email);
                $email = str_replace( "%{Date}", date("Y-m-d H:i:s"), $email);
                $lines = explode("\n", $email);
                $subject = $lines[0]; // The first line is subject.
                $text = preg_replace('/^.*\n/', '', $email); // Everything except the first line.
                if (!Core::sendEmail($to, trim($subject), $text))
                {
                    Core::logError(Error::create(Error::levelWarning, "E-mail could not be sent to {$to}."));
                }
            }
        }
        return true;

	}
}

