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
 * @copyright 2017 Cerema — Alexandre Tranchant
 * @license   Propriétaire Cerema
 */

namespace App\Manager;

use App\Bean\Factory\LogFactory;
use App\Entity\PaginatorInterface;
use App\Entity\Ip;
use App\Entity\Network;
use App\Entity\User;
use App\Repository\IpRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * Ip Manager.
 *
 * @category Manager
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 */
class IpManager implements LoggableManagerInterface, PaginatorInterface
{
    /**
     * Const for the alias query.
     */
    const ALIAS = 'ip';

    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Repository.
     *
     * @var IpRepository
     */
    private $repository;

    /**
     * IpManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->repository = $this->em->getRepository(Ip::class);
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
     * Delete ip without verification.
     *
     * @param Ip $ip
     */
    public function delete(Ip $ip)
    {
        $this->em->remove($ip);
        $this->em->flush();
    }

    /**
     * Return all free IP.
     * A free IP is an IP¨without linked machine.
     *
     * @param Network $network
     *
     * @return Ip[]
     */
    public function getFree(Network $network)
    {
        return $this->repository->findBy([
            'network' => $network,
            'machine' => null,
        ]);
    }

    /**
     * Return IP from its id.
     *
     * @param int $id
     *
     * @return Ip|null
     */
    public function getById(int $id):?Ip
    {
        /** @var Ip $ip */
        $ip = $this->repository->findOneBy([
            'id' => $id,
        ]);

        return $ip;
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

    /**
     * Is this entity deletable?
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(): bool
    {
        //An IP is always deletable.
        return true;
    }

    /**
     * Retrieve logs of the axe.
     *
     * @param Ip $entity
     *
     * @return array
     */
    public function retrieveLogs($entity): array
    {
        /** @var LogEntryRepository $logRepository */
        $logRepository = $this->em->getRepository(LogEntry::class); // we use default log entry class
        $logs = $logRepository->getLogEntries($entity);

        return LogFactory::createIpLogs($logs);
    }

    /**
     * Save new or modified Ip.
     *
     * @param Ip   $ip
     * @param User $user
     */
    public function save(Ip $ip, User $user = null)
    {
        if ($user && empty($ip->getCreator())) {
            $ip->setCreator($user);
        }
        $this->em->persist($ip);
        $this->em->flush();
    }

    /**
     * Unlink an IP from its machine.
     *
     * @param Ip $ip
     */
    public function unlink(Ip $ip)
    {
        $ip->setMachine(null);
        $this->save($ip);
    }
}
