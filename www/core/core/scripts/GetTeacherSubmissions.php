<?php

namespace asm\core;
use asm\core\lang\Language;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Gets all submissions for assignments that are owned by the active user.
 * @n @b Requirements: User::submissionsCorrect privilege
 * @n @b Arguments: none
 */
final class GetTeacherSubmissions extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::submissionsCorrect))
			return false;

        $canViewAuthors = User::instance()->hasPrivileges(User::submissionsViewAuthors);
        $rated = $this->getParams('rated') ? true : false;
        $all = $this->getParams('all') ? true : false;
        $absolutelyAll = $this->getParams('absolutelyAll') ? true : false;
        $userId = User::instance()->getId();

        if ($absolutelyAll) {
            if (!$this->userHasPrivileges(User::lecturesManageAll, User::groupsManageAll, User::otherAdministration)) {
                return false;
            }
        }

        /**
         * @var $submissions \Submission[]
         */
        // group is a DQL reserved word, so we must use _group

        if ($absolutelyAll)
        {
            $submissions = Repositories::makeDqlQuery("SELECT submission, user, assignment, problem, _group FROM \Submission submission JOIN submission.assignment assignment JOIN assignment.problem problem JOIN submission.user user JOIN assignment.group _group WHERE submission.status <> 'deleted'")->getResult();
        }
        else {
            $submissions = Repositories::makeDqlQuery("SELECT submission, user, assignment, problem, _group FROM \Submission submission JOIN submission.assignment assignment JOIN assignment.problem problem JOIN submission.user user JOIN assignment.group _group WHERE _group.owner = :submissionCorrector AND (submission.status = 'graded' OR submission.status = 'latest' OR submission.status = 'handsoff') AND _group.deleted = 0")->setParameter('submissionCorrector', $userId)->getResult();
        }

        foreach($submissions as $submission)
        {
            if (!$all && !$absolutelyAll)
            {
                if ($rated && $submission->getStatus() !== \Submission::STATUS_GRADED)
                {
                    continue;
                }
                if (!$rated && $submission->getStatus() === \Submission::STATUS_GRADED)
                {
                    continue;
                }
            }
            $descriptionForTeacher = $submission->getInfo();
            if ($submission->getSimilarityStatus() == \Submission::SIMILARITY_STATUS_GUILTY) {
                $descriptionForTeacher = Language::get(StringID::ThisSubmissionIsPlagiarism) . "\n======\n" . $descriptionForTeacher;
            }
            if ($submission->getSimilarityStatus() == \Submission::SIMILARITY_STATUS_INNOCENT) {
                $descriptionForTeacher = $descriptionForTeacher . "\n======\n" . Language::get(StringID::ThisSubmissionIsInnocent);
            }
            if ($submission->getSimilarityStatus() == \Submission::SIMILARITY_STATUS_NEW) {
                $descriptionForTeacher = $descriptionForTeacher . "\n======\n" . Language::get(StringID::ThisHasYetToBeCheckedForPlagiarism);
            }
            if ($submission->getStatus() == \Submission::STATUS_REQUESTING_GRADING) {
                $descriptionForTeacher = Language::get(StringID::GradingRequested) . " " . $descriptionForTeacher;
            }
            $row = [
                $submission->getId(),
                $submission->getAssignment()->getProblem()->getName(),
                $submission->getAssignment()->getGroup()->getName(),
                $submission->getDate()->format("Y-m-d H:i:s"),
                $submission->getSuccess(),
                $descriptionForTeacher,
                $submission->getRating(),
                $submission->getExplanation(),
                $submission->getAssignment()->getReward(),
                $submission->getAssignment()->getDeadline()->format("Y-m-d H:i:s"),
                ($canViewAuthors ? $submission->getUser()->getId() : 0),
                ($canViewAuthors ? $submission->getUser()->getRealName(): Language::get(StringID::NotAuthorizedForName)),
                ($submission->getOutputfile() != ''),
                $submission->getAssignment()->getId()
            ];
            if ($absolutelyAll) {
                $row[] = ($canViewAuthors ? $submission->getUser()->getEmail(): Language::get(StringID::NotAuthorizedForName));
                $row[] = $submission->getStatus();
            }
            $this->addRowToOutput($row);
        }
        return true;
	}
}

