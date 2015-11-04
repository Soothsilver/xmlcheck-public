<?php
namespace asm\core;


/**
 * Contains classes that return results from database for complicated queries that are used across multiple requests.
 */
class CommonQueries {

    /**
     * Returns an array of all attachments that can be viewed and deleted by the logged-in user.
     *
     * @return \Attachment[] attachments that can be edited by the active user
     */
    public static function GetAttachmentsVisibleToActiveUser()
    {
        $repository = Repositories::getRepository(Repositories::Attachment);
        if (User::instance()->hasPrivileges(User::lecturesManageAll))
        {
            return $repository->findAll();
        }
        else
        {
            return  Repositories::getEntityManager()->createQuery("SELECT a FROM \Attachment a JOIN a.lecture l WHERE l.owner = :ownerId")
                ->setParameter('ownerId', User::instance()->getId())
                ->getResult();
        }
    }
    /**
     * Returns an array of all questions that can be viewed, edited and deleted by the logged-in user.
     *
     * @return \Question[] questions that can be edited by the active user
     */
    public static function GetQuestionsVisibleToActiveUser()
    {
        $repository = Repositories::getRepository(Repositories::Question);
        if (User::instance()->hasPrivileges(User::lecturesManageAll))
        {
            return $repository->findAll();
        }
        else
        {
            return  Repositories::getEntityManager()->createQuery("SELECT q FROM \Question q JOIN q.lecture l WHERE l.owner = :ownerId")
                ->setParameter('ownerId', User::instance()->getId())
                ->getResult();
        }
    }

    /**
     * Returns an array of all tests that can be viewed, edited and deleted by the logged-in user.
     *
     * @return \Xtest[] tests that can be edited by the active user
     */
    public static function GetTestsVisibleToUser()
    {
        $repository = Repositories::getRepository(Repositories::Xtest);
        if (User::instance()->hasPrivileges(User::lecturesManageAll))
        {
            return $repository->findAll();
        }
        else
        {
            return  Repositories::getEntityManager()->createQuery("SELECT x FROM \Xtest x JOIN x.lecture l WHERE l.owner = :ownerId")
                ->setParameter('ownerId', User::instance()->getId())
                ->getResult();
        }
    }
} 