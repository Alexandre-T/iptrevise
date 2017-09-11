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
use App\Entity\Machine;
use App\Repository\MachineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * Machine Manager.
 *
 * @category Manager
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 */
class MachineManager implements LoggableManagerInterface, PaginatorInterface
{
    /**
     * Const for the alias query.
     */
    const ALIAS = 'machine';

    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Repository.
     *
     * @var MachineRepository
     */
    private $repository;

    /**
     * MachineManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->repository = $this->em->getRepository(Machine::class);
    }

    /**
     * Delete machine without verification.
     *
     * @param Machine $machine
     */
    public function delete(Machine $machine)
    {
        $this->em->remove($machine);
        $this->em->flush();
    }

    /**
     * Is this entity deletable?
     *
     * @param Machine $machine
     * @return bool true if entity is deletable
     */
    public function isDeletable(Machine $machine): bool
    {
        //A machine is deletable if there is no IPs referenced.
        return (0 == count($machine->getIps()));
    }

    /**
     * Retrieve logs of the axe.
     *
     * @param Machine $entity
     *
     * @return array
     */
    public function retrieveLogs($entity): array
    {
        /** @var LogEntryRepository $logRepository */
        $logRepository = $this->em->getRepository(LogEntry::class); // we use default log entry class
        $logs = $logRepository->getLogEntries($entity);

        return LogFactory::createMachineLogs($logs);
    }

    /**
     * Save new or modified Machine.
     *
     * @param Machine $machine
     */
    public function save(Machine $machine)
    {
        $this->em->persist($machine);
        $this->em->flush();
    }

    /**
     * Return the Query builder needed by the paginator.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        $qb = $this->repository->createQueryBuilder(self::ALIAS);

        $qb ->leftJoin(self::ALIAS.'.ips', 'ips')
            ->addSelect('COUNT(ips.id) AS ipsCount')
            ->groupBy(self::ALIAS.'.id');

        return $qb;
    }
}