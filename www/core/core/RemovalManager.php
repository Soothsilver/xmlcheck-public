<?php

namespace asm\core;
use asm\utils\Filesystem;

/**
 * Methods for removal of various item types from database with dependencies @module.
 */
class RemovalManager
{
    /**
     * @param $user \User
     */
    public static function hideUserAndAllHeOwns($user)
    {
        $user->setDeleted(true);
        $user->setEmail("= ACCOUNT DELETED =");
        $user->setPass("= ACCOUNT DELETED =");
        $user->setSendEmailOnNewAssignment(0);
        $user->setSendEmailOnNewSubmission(0);
        $user->setSendEmailOnSubmissionRated(0);
        $user->setResetLink("");
        Repositories::persist($user);
        foreach($user->getGroups() as $group)
        {
            self::hideGroupAndItsAssignments($group);
        }
        foreach($user->getLectures() as $lecture)
        {
            self::hideLectureItsProblemsGroupsQuestionsAttachmentsAndXtests($lecture);
        }
        foreach ($user->getSubmissions() as $submission)
        {
            $submission->setStatus(\Submission::STATUS_DELETED);
            Repositories::persist($submission);
        }
        Repositories::flushAll();
    }
    /**
     * @param $group \Group
     */
    public static function hideGroupAndItsAssignments($group)
    {
        $group->setDeleted(true);
        Repositories::persist($group);
        foreach($group->getAssignments() as $assignment)
        {
            $assignment->setDeleted(true);
            Repositories::getEntityManager()->persist($assignment);
        }
        Repositories::flushAll();
    }

    /**
     * @param $problem \Problem
     */
    public static function hideProblemAndItsAssignments($problem)
    {
        $problem->setDeleted(true);
        Repositories::persist($problem);
        foreach($problem->getAssignments() as $assignment)
        {
            $assignment->setDeleted(true);
            Repositories::getEntityManager()->persist($assignment);
        }
        Repositories::flushAll();
    }

    /**
     * @param $lecture \Lecture
     */
    public static function hideLectureItsProblemsGroupsQuestionsAttachmentsAndXtests($lecture)
    {
        $lecture->setDeleted(true);
        Repositories::persist($lecture);
        foreach($lecture->getProblems() as $problem)
        {
            self::hideProblemAndItsAssignments($problem);
        }
        foreach($lecture->getGroups() as $group)
        {
            self::hideGroupAndItsAssignments($group);
        }


        // Order is important because of foreign key constraints on the database.
        foreach ($lecture->getAttachments() as $attachment)
        {
            Repositories::remove($attachment);
        }
        foreach ($lecture->getQuestions() as $question)
        {
            Repositories::remove($question);
        }
        foreach($lecture->getXtests() as $xtest)
        {
            Repositories::remove($xtest);
        }


        Repositories::persistAndFlush($lecture);
    }




	/**
	 * Deletes attachment with supplied ID. Removes the attachment from questions that referenced it. This will not delete the attachment file from disk.
     *
	 * @param int $id attachment ID
	 * @return array error properties provided by removalError() or retrievalError(),
	 * or false in case of success
	 */
	public static function deleteAttachmentById ($id)
	{
        /**
         * @var $attachment \Attachment
         */
        $attachment = Repositories::findEntity(Repositories::Attachment, $id);
        $questions = CommonQueries::getQuestionsVisibleToActiveUser();
        foreach($questions as $question)
        {
            $modificationMade = false;
            $attachments = explode(';', $question->getAttachments());
            for ($i = 0; $i < count($attachments); $i++)
            {
                if ($attachments[$i] === (string)$id)
                {
                    unset($attachments[$i]);
                    $modificationMade = true;
                }
            }
            if ($modificationMade)
            {
                $question->setAttachments(implode(';', $attachments));
                Repositories::persistAndFlush($question);
            }
        }
        Repositories::remove($attachment);
        return false;
	}

	/**
	 * Deletes plugin with supplied ID. This is a very destructive operation because all problems associated with this plugin will lose any reference to it and thus the submissions will lose reference and therefore we won't be able to use them for sooth.similarity comparison, for example.
     *
	 * @param int $id plugin ID
	 * @return array error properties provided by removalError() or retrievalError(),
	 * or false in case of success
	 */
	public static function deletePluginById ($id)
	{
        /**
         * @var $plugin \Plugin
         * @var $tests \PluginTest[]
         * @var $problems \Problem[]
         */
        $plugin = Repositories::findEntity(Repositories::Plugin, $id);

        // Destroy plugin tests testing this plugin
        $tests = Repositories::getRepository(Repositories::PluginTest)->findBy(['plugin' => $id]);
		foreach ($tests as $test)
		{
			self::deleteTestById($test->getId());
        }

        // Problems that relied on this plugin are now without plugin
        $problems = Repositories::getRepository(Repositories::Problem)->findBy(['plugin' => $id]);
        foreach ($problems as $problem)
        {
            $problem->setPlugin(null);
            Repositories::persist($problem);
        }
        Repositories::flushAll();

        // Delete the plugin
        Repositories::remove($plugin);
        return false;
	}

	/**
	 * Deletes test with supplied ID (with input & output files).
	 * @param int $id test ID
	 * @return array error properties provided by removalError() or retrievalError(),
	 * or false in case of success
	 */
	public static function deleteTestById ($id)
	{
        /**
         * @var $test \PluginTest
         */
        $test = Repositories::findEntity(Repositories::PluginTest, $id);
        $testFolder = Config::get('paths', 'tests');

        // Delete input solution file
        if (is_file(Filesystem::combinePaths($testFolder, $test->getInput())))
        {
            Filesystem::removeFile(Filesystem::combinePaths($testFolder, $test->getInput()));
        }
        // Delete plugin test output
        if (is_file(Filesystem::combinePaths($testFolder, $test->getOutput())))
        {
            Filesystem::removeFile(Filesystem::combinePaths($testFolder, $test->getOutput()));
        }
        Repositories::remove($test);
		return false;
	}

}

