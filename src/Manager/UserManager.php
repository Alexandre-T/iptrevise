<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema
 * @license   CeCILL-B V1
 *
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

namespace App\Manager;

use App\Bean\Factory\LogFactory;
use App\Entity\PaginatorInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * User Manager.
 *
 * @category Manager
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class UserManager implements LoggableManagerInterface, PaginatorInterface
{
    /**
     * Const for the alias query.
     */
    const ALIAS = 'user';

    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Repository.
     *
     * @var UserRepository
     */
    private $repository;

    /**
     * UserManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->repository = $this->em->getRepository(User::class);
    }

    /**
     * Return the number of networks registered in database.
     *
     * @return int
     */
    public function count()
    {
        return $this->repository->createQueryBuilder(self::ALIAS)
            ->select('COUNT(1)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Delete user without verification.
     *
     * @param User $user
     */
    public function delete(User $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * Is this entity deletable?
     *
     * @param  User $user
     * @return bool true if entity is deletable
     */
    public function isDeletable(User $user): bool
    {
        //FIXME We cannot delete a user which have create an object.
        $id = $user->getId();
        return true;
    }

    /**
     * Retrieve logs of the axe.
     *
     * @param User $entity
     *
     * @return array
     */
    public function retrieveLogs($entity): array
    {
        /** @var LogEntryRepository $logRepository */
        $logRepository = $this->em->getRepository(LogEntry::class); // we use default log entry class
        $logs = $logRepository->getLogEntries($entity);

        return LogFactory::createUserLogs($logs);
    }

    /**
     * Save new or modified User.
     *gr
     * @param User $user
     */
    public function save(User $user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Return the Query builder needed by the paginator.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->repository->createQueryBuilder(self::ALIAS);
    }
}
