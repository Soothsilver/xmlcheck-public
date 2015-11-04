<?php

namespace asm\core;
use asm\core\lang\StringID;


/**
 * @ingroup requests
 * Updates submission with supplied rating.
 * @n @b Requirements: user has to own (transitively) the assignment submission belongs to
 * @n @b Arguments:
 * @li @c id submission id
 * @li @c rating submission rating
 */
final class RateSubmission extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::submissionsCorrect))
			return false;

		$inputs = array(
			'id' => 'isIndex',
			'rating' => 'isNonNegativeInt',
            'explanation' => null
		);
		if (!$this->isInputValid($inputs))
			return false;

		$id = $this->getParams('id');
		$rating = $this->getParams('rating');
		$explanation = $this->getParams('explanation');

		/**
		 * @var $submission \Submission
		 */
		$submission = Repositories::findEntity(Repositories::Submission, $id);
		$user = User::instance();
		if ($submission->getAssignment()->getGroup()->getOwner()->getId() !== $user->getId()) {
			return $this->death(StringID::InsufficientPrivileges);
		}
		$status = $submission->getStatus();
		if ($status === \Submission::STATUS_BEING_EVALUATED)
		{
			// Submissions that are still being evaluated are not ever shown to the tutor in the user interface
			return $this->death(StringID::HackerError);
		}
		else if ($status === \Submission::STATUS_NORMAL)
		{
			// This may happen if the tutor downloads a student's latest submission, and while his or her browser is still open,
			// the student uploads another submission, therefore the original submission is now NORMAL rather than LATEST.
			// Still, the tutor should have retain the right to grade it. If he or she grades it after deadline, then it's the
			// student's fault for submitting the second solution late. If the deadline has not yet passed, then it's the tutor's
			// fault for attempting to grade it. Either way, we should proceed as normal.
		}
		else if ($status === \Submission::STATUS_DELETED)
		{
			// This may happen similarly to the previous case: The student may upload a new submission and delete the old one.
			// However, if the tutor had the right to grade it before its deletion, then the student may not prevent the grading
			// by deleting it. Therefore, we should proceed as normal.
		}
		else if ($status === \Submission::STATUS_GRADED)
		{
			if (!$user->hasPrivileges(User::submissionsModifyRated)) {
				return $this->death(StringID::InsufficientPrivileges);
			}
		}
		else
		{
			// This submission is either LATEST or REQUESTING_GRADING, therefore it is ok to grade it.
			// This is the happy path.
		}
		$maxReward = $submission->getAssignment()->getReward();
		if ($rating > $maxReward) {
			return $this->stop('rating exceeds assignment\'s maximum reward');
		}

        // First, all previously graded submissions of this user for this assignment are annulled.
        /**
         * @var $previouslySubmissions \Submission[]
         */
        $previouslySubmissions = Repositories::getRepository(Repositories::Submission)->findBy([
            'status' => \Submission::STATUS_GRADED,
            'assignment' => $submission->getAssignment()->getId(),
            'user' => $submission->getUser()->getId()
        ]);
        foreach ($previouslySubmissions as $previousSubmission) {
            $previousSubmission->setStatus(\Submission::STATUS_NORMAL);
            Repositories::persist($previousSubmission);
        }


        $submission->setStatus(\Submission::STATUS_GRADED);
		$submission->setRating($rating);
		$submission->setExplanation($explanation);
		Repositories::flushAll();

		// Now send email.
		$student = $submission->getUser();
        if ($student->getSendEmailOnSubmissionRated())
        {
			// Load email.
            $email = file_get_contents(Config::get("paths", $rating == $maxReward ? "successEmail" : "failureEmail"));
            $email = str_replace( "%{Points}", $rating, $email);
            $email = str_replace( "%{Maximum}", $maxReward, $email);
            $email = str_replace( "%{Explanation}", $explanation, $email);
            $email = str_replace( "%{Assignment}", $submission->getAssignment()->getProblem()->getName(), $email);
            $email = str_replace( "%{Link}", Config::getHttpRoot() . "#submissions", $email);
            $email = str_replace( "%{Date}", date("Y-m-d H:i:s"), $email);
            $lines = explode("\n", $email);
            $subject = $lines[0];
            $text = preg_replace('/^.*\n/', '', $email);
            if (!Core::sendEmail($student->getEmail(), trim($subject), $text))
            {
                $this->death(StringID::MailError);
            }
        }
		return true;
	}
}

