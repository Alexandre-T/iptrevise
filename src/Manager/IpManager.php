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
use App\Entity\Ip;
use App\Entity\Network;
use App\Entity\User;
use App\Repository\IpRepository;
use Countable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * Ip Manager.
 *
 * @category Manager
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
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
        $nIp = is_array($ips) || $ips instanceof Countable ? count($ips) : 0;

        //We do this test before the loop for optimization
        if ($nIp == $network->getCapacity() || null === $network->getIp()) {
            // All IP is referenced.
            return null;
        }

        $index = $network->getMinIp();
        $cursor = 0;
        while ($index <= $network->getMaxIp()) {
            if ($cursor > ($nIp - 1)) {
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
     * @return Ip|null
     */
    public function getById(int $id): ?Ip
    {
        /** @var Ip $ip */
        $ip = $this->repository->findOneBy([
            'id' => $id,
        ]);

        return $ip;
    }

    /**
     * Return all ips.
     *
     * @return Ip[]
     */
    public function getAll()
    {
        return $this->repository->findAll();
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
     * @param Ip $ip
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(Ip $ip = null): bool
    {
        //An IP is always deletable.
        return $ip instanceof Ip;
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

    /**
     * Return the query builder for the search.
     *
     * @param string $search
     * @param array $sites
     * @return QueryBuilder
     */
    public function getQueryBuilderWithSearch(string $search, array $sites)
    {
        $searchText = trim($search);
        $searchIps = [];
        foreach (explode(' ', $searchText) as $searchIp) {
            $ip = ip2long(str_replace('%','', $searchIp));
            if (false !== $ip) {
                $searchIps[] = $ip;
            }
        }

        $qb = $this->getQueryBuilder();
        $qb->join('ip.machine', 'machine')
            ->join('ip.network', 'network')
            ->where('network.site in (:sites)')
            ->andWhere('lower(ip.reason) like :search or lower(machine.label) like :search or lower(network.label) like :search or ip.ip in (:searchIp)')
            ->setParameter('sites', $sites)
            ->setParameter('search', $search)
            ->setParameter('searchIp', $searchIps);

        return $qb;
    }
}
