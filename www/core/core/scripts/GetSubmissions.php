<?php

namespace asm\core;


/**
 * @ingroup requests
 * Gets user's submissions.
 * @n @b Requirements: User::assignmentsSubmit privilege
 * @n @b Arguments: none
 */
final class GetSubmissions extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::assignmentsSubmit))
			return;

        /**
         * @var $submissions \Submission[]
         */
        $submissions = Repositories::getRepository(Repositories::Submission)->findBy(['user'=>User::instance()->getId()]);
        foreach($submissions as $submission)
        {
            if ($submission->getStatus() == \Submission::STATUS_DELETED) { continue; }
            $row =
                [
                    $submission->getId(),
                    $submission->getAssignment()->getProblem()->getName(),
                    $submission->getAssignment()->getDeadline()->format("Y-m-d H:i:s"),
                    $submission->getDate()->format("Y-m-d H:i:s"),
                    $submission->getStatus(),
                    $submission->getSuccess(),
                    $submission->getInfo(),
                    $submission->getRating(),
                    $submission->getExplanation(),
                    ($submission->getOutputfile() != '')
                ];
            $this->addRowToOutput($row);
        }
	}
}

