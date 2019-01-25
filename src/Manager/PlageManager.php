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
use App\Entity\Plage;
use App\Entity\Ip;
use App\Entity\Network;
use App\Entity\User;
use App\Repository\PlageRepository;
use Countable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * Plage Manager.
 *
 * @category Manager
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class PlageManager implements LoggableManagerInterface, PaginatorInterface
{
    /**
     * Const for the alias query.
     */
    const ALIAS = 'plage';

    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Repository.
     *
     * @var PlageRepository
     */
    private $repository;

    /**
     * PlageManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->repository = $this->em->getRepository(Plage::class);
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
     * Delete plage without verification.
     *
     * @param Plage $plage
     */
    public function delete(Plage $plage)
    {
        $this->em->remove($plage);
        $this->em->flush();
    }

    /**
     * Return all free Plage.
     * A free Plage is a Plage¨without reserved ip.
     *
     * @param Network $network
     *
     * @return Plage[]
     */
    public function getFree(Network $network)
    {
        return $this->repository->findBy([
            'network' => $network,
            // TODO
        ]);
    }

    /**
     * Return the first non-referenced ip.
     *
     * @param Network $network
     *
     * @return null|int
     */
    public function getFirstNonReferencedIp(Network $network)
    {
        /** @var Ip[] $ips */
        $ips = $this->repository->findBy(['network' => $network], ['ip' => 'ASC']);

        //We do this test before the loop for optimization
        if (count($ips) == $network->getCapacity() || null === $network->getIp()) {
            // All IP is referenced.
            return null;
        }

        $index = $network->getMinIp();
        $cursor = 0;
        while ($index <= $network->getMaxIp()) {
            if ($cursor > count($ips) - 1) {
                return $index;
            }

            if ($index < $ips[$cursor]->getIp()) {
                return $index;
            } elseif ($index == $ips[$cursor]->getIp()) {
                ++$index;
            } else {
                ++$cursor;
            }
        }

        return null;
    }

    /**
     * Return IP from its id.
     *
     * @param int $id
     *
     * @return Plage|null
     */
    public function getById(int $id): ?Plage
    {
        /** @var Plage $ip */
        $plage = $this->repository->findOneBy([
            'id' => $id,
        ]);

        return $plage;
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
     * @param Plage $plage
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(Plage $plage = null): bool
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
     * @param Plage   $plage
     * @param User $user
     */
    public function save(Plage $plage, User $user = null)
    {
        if ($user && empty($plage->getCreator())) {
            $plage->setCreator($user);
        }
        $this->em->persist($plage);
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
