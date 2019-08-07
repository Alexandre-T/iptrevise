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
use App\Entity\Machine;
use App\Entity\User;
use App\Repository\MachineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * Machine Manager.
 *
 * @category Manager
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
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
     * Return the number of networks registered in database.
     *
     * @return int
     */
    public function count()
    {
        try {
            return $this->repository->createQueryBuilder(self::ALIAS)
                ->select('COUNT(1)')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
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
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(Machine $machine): bool
    {
        //A machine is deletable if there is no IPs referenced.
        return 0 == count($machine->getIps());
    }

    /**
     * Return all machines.
     *
     * @return Machine[]
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Get Machine with given id.
     *
     * @param $machineId int
     *
     * @return Machine | null
     */
    public function getMachineById(int $machineId): ?Machine
    {
        /** @var Machine $machine */
        $machine = $this->repository->findOneBy(['id' => $machineId]);

        return $machine;
    }

    /**
     * Get Machine with given label.
     *
     * @param $machineLab string
     *
     * @return Machine | null
     */
    public function getMachineByLabel(string $machineLab): ?Machine
    {
        /** @var Machine $machine */
        $machine = $this->repository->findOneBy(['label' => $machineLab]);

        return $machine;
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
     * @param User    $user
     */
    public function save(Machine $machine, User $user = null)
    {
        if ($user && empty($machine->getCreator())) {
            $machine->setCreator($user);
        }
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

        $qb->leftJoin(self::ALIAS.'.ips', 'ips')
           ->leftJoin(self::ALIAS.'.tags', 'tags')
            ->select('machine')
           ->addSelect('COUNT(DISTINCT ips.id) AS ipsCount')
           ->addSelect("string_agg(tags.label, ',') AS tagsConcat")
           ->groupBy(self::ALIAS);

        return $qb;
    }

    public function getQueryBuilderWithSearch(string $search)
    {
        $search = strtolower($search);
        $qb = $this->getQueryBuilder();
        $qb->where('lower(machine.label) like :search')
            ->orWhere('lower(machine.description) like :search')
            ->orWhere('lower(machine.location) like :search')
            ->orWhere('lower(machine.macs) like :search')
            ->orWhere('lower(tags.label) like :search')
            ->setParameter('search', $search);

        return $qb;
    }
}
