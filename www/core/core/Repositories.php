<?php

namespace asm\core;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * Provides functions to access the database.
 */
class Repositories
{
    /**
     * The ID of the usertype STUDENT. This ID must be 1. It is special because it cannot be deleted and because whenever
     * a usertype is erased, all users of that usertype become users with the STUDENT usertype.
     */
    const StudentUserType = 1;

    // Entity names
    /**
     * Name of the Assignment entity.
     */
    const Assignment = "Assignment";
    /**
     * Name of the Group entity.
     */
    const Group = "Group";
    /**
     * Name of the Lecture entity.
     */
    const Lecture = "Lecture";
    /**
     * Name of the Plugin entity.
     */
    const Plugin = "Plugin";
    /**
     * Name of the Problem entity.
     */
    const Problem = "Problem";
    /**
     * Name of the Submission entity.
     */
    const Submission = "Submission";
    /**
     * Name of the Subscription entity.
     */
    const Subscription = "Subscription";
    /**
     * Name of the User entity.
     */
    const User = "User";
    /**
     * Name of the UserType entity.
     */
    const UserType = "UserType";
    /**
     * Name of the PluginTest entity.
     */
    const PluginTest = "PluginTest";
    /**
     * Name of the Question entity.
     */
    const Question = "Question";
    /**
     * Name of the XTest entity. This represents a printable test.
     */
    const Xtest = "Xtest";
    /**
     * Name of the Similarity entity.
     */
    const Similarity = "Similarity";
    /**
     * Name of the Attachment entity.
     */
    const Attachment = "Attachment";
    /**
     * @var EntityManager
     */
    private static $entityManager = null;

    /**
     * Connects to the database using configuration from the config files and saves the entity manager in a private
     * static local variable. This function is called the first time getEntityManager is called.
     *
     * As a prerequisite, you must call Config::init() before calling this method.
     * @throws \Doctrine\DBAL\DBALException When the database connection fails.
     * @throws \Doctrine\ORM\ORMException When there is an error in the entity classes.
     * @throws \Exception When there is a different error.
     */
    private static function connectToDatabase()
    {
        $isDevMode = true;
        $connection = array(
            'driver'   => 'pdo_mysql',
            'user'     => Config::get('database', 'user'),
            'password' => Config::get('database', 'pass'),
            'dbname'   => Config::get('database', 'db'),
            'host'     => Config::get('database', 'host'),
            'charset'  => 'utf8'
        );
        $paths = array(__DIR__ . "/../doctrine");
        $config = Setup::createConfiguration($isDevMode);
        $driver = new AnnotationDriver(new AnnotationReader(), $paths);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);
        $entityManager = EntityManager::create($connection, $config);
        $platform = $entityManager->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
        self::$entityManager = $entityManager;
    }

    /**
     * Creates a Doctrine Query from DQL code.
     *
     * @param string $query DQL code.
     * @return \Doctrine\ORM\Query A Doctrine2 query object.
     */
    public static function makeDqlQuery($query)
    {
        return self::getEntityManager()->createQuery($query);
    }

    /**
     * Returns the entity with the specified ID or null if such an entity does not exist.
     *
     * @param string $entityName Name of the entity to search for. It is recommended to use the constants in this class such as Repositories::User.
     * @param int $id ID of the entity to search for.
     * @return null|object The entity with the specified ID.
     * @throws \Doctrine\ORM\ORMException When there is an error in the entity classes.
     * @throws \Doctrine\ORM\OptimisticLockException This should never be thrown.
     * @throws \Doctrine\ORM\TransactionRequiredException This should never be thrown.
     */
    public static function findEntity($entityName, $id)
    {
        return self::getEntityManager()->find($entityName, $id);
    }

    /**
     * Removes the entity from the database and flushes this removal.
     * @param object $entity the entity to remove from the database
     */
    public static function remove($entity)
    {
        self::getEntityManager()->remove($entity);
        self::getEntityManager()->flush($entity);
    }

    /**
     * Gets the Doctrine repository for the specified entity name.
     *
     * @param $entityName string name of the entity
     * @return \Doctrine\ORM\EntityRepository the repository
     */
    public static function getRepository($entityName)
    {
        return self::getEntityManager()->getRepository($entityName);
    }

    /**
     * Calls the Doctrine persist() method on the specified entity.
     *
     * @param object $entity The entity to persist.
     */
    public static function persist($entity)
    {
        self::getEntityManager()->persist($entity);
    }

    /**
     * Calls the Doctrine persist() and flush() methods on the specified entity.
     * @param object $entity The entity to persist and flush.
     */
    public static function persistAndFlush($entity)
    {
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush($entity);
    }

    /**
     * Flushes the entity manager.
     */
    public static function flushAll()
    {
        self::getEntityManager()->flush();
    }
    /**
     * Connects to the database if there is not a connection yet, and returns a Doctrine entity manager.
     * @return EntityManager The entity manager connected to the database.
     */
    public static function getEntityManager()
    {
        if (self::$entityManager === null)
        {
            self::connectToDatabase();
        }
        return self::$entityManager;
    }
} 