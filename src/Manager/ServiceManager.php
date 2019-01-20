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
//TODO adapter au service
namespace App\Manager;

use App\Bean\Factory\LogFactory;
use App\Entity\PaginatorInterface;
use App\Entity\Service;
use App\Entity\User;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * Service Manager.
 *
 * @category Manager
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class ServiceManager implements LoggableManagerInterface, PaginatorInterface
{
    /**
     * Const for the alias query.
     */
    const ALIAS = 'service';

    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Repository.
     *
     * @var ServiceRepository
     */
    private $repository;

    /**
     * ServiceManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->repository = $this->em->getRepository(Service::class);
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
     * Delete service without verification.
     *
     * @param Service $service
     */
    public function delete(Service $service)
    {
        $this->em->remove($service);
        $this->em->flush();
    }

    /**
     * Is this entity deletable?
     *
     * @param Service $service
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(Service $service): bool
    {
        //A service is deletable if there is no IPs referenced.
        return 0 == count($service->getMachines());
    }

    /**
     * Return all services.
     *
     * @return Service[]
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Get service with given id.
     *
     * @param $serviceId int
     *
     * @return Service | null
     */
    public function getServiceById(int $serviceId): ?Service
    {
        /** @var Service $service */
        $service = $this->repository->findOneBy(['id' => $serviceId]);

        return $service;
    }

    /**
     * Retrieve logs of the axe.
     *
     * @param Service $entity
     *
     * @return array
     */
    public function retrieveLogs($entity): array
    {
        /** @var LogEntryRepository $logRepository */
        $logRepository = $this->em->getRepository(LogEntry::class); // we use default log entry class
        $logs = $logRepository->getLogEntries($entity);

        return LogFactory::createServiceLogs($logs);
    }

    /**
    * Save new or modified Service.
    *
    * @param Service $service
    */
    public function save(Service $service)
    {
        $this->em->persist($service);
        $this->em->flush();
    }

     /** Return the Query builder needed by the paginator.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        $qb = $this->repository->createQueryBuilder(self::ALIAS);

        $qb->leftJoin(self::ALIAS.'.machines', 'machines')
           ->addSelect('COUNT(DISTINCT machines.id) AS machinesCount')
           ->groupBy(self::ALIAS.'.id');

        return $qb;
    }
}
