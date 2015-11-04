<?php

namespace asm\core;


/**
 * This request allows a user to change what emails he will receive.
 */
final class EditEmailNotificationOptions extends DataScript
{
    protected function body ()
	{
        if (!$this->userHasPrivileges())
             return false;

        $onSubmissionRated = $this->paramExists(User::sendEmailOnSubmissionRatedStudent);
        $onSubmissionConfirmed =  $this->paramExists(User::sendEmailOnSubmissionConfirmedTutor);
        $onAssignmentAvailable = $this->paramExists(User::sendEmailOnAssignmentAvailableStudent);
        $userEntity = User::instance()->getEntity();

        User::instance()->setData(User::sendEmailOnSubmissionRatedStudent, $this->paramExists(User::sendEmailOnSubmissionRatedStudent)  ? 1 : 0);
        User::instance()->setData(User::sendEmailOnSubmissionConfirmedTutor, $this->paramExists(User::sendEmailOnSubmissionConfirmedTutor) ? 1 : 0);
        User::instance()->setData(User::sendEmailOnAssignmentAvailableStudent, $this->paramExists(User::sendEmailOnAssignmentAvailableStudent) ? 1 : 0);

        // Update
        $userEntity->setSendEmailOnNewAssignment(!!$onAssignmentAvailable);
        $userEntity->setSendEmailOnNewSubmission(!!$onSubmissionConfirmed);
        $userEntity->setSendEmailOnSubmissionRated(!!$onSubmissionRated);
        Repositories::getEntityManager()->persist($userEntity);
        Repositories::getEntityManager()->flush($userEntity);

        return true;
	}
}

