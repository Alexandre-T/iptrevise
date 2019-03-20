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
use App\Entity\Network;
use App\Entity\User;
use App\Repository\NetworkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Network Manager.
 *
 * @category Manager
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class NetworkManager implements LoggableManagerInterface, PaginatorInterface
{
    /**
       * Const for the alias query.
     */
    const ALIAS = 'network';

    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Repository.
     *
     * @var NetworkRepository
     */
    private $repository;

    /**
     * NetworkManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->repository = $this->em->getRepository(Network::class);
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
     * Delete network without verification.
     *
     * @param Network $network
     */
    public function delete(Network $network)
    {
        $this->em->remove($network);
        $this->em->flush();
    }

    /**
     * Is this entity deletable?
     *
     * @param Network $network
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(Network $network): bool
    {
        //A network is deletable if there is no IPs referenced.
        return 0 == count($network->getIps());
    }

    /**
     * Return all networks.
     *
     * @return Network[]|null Array of network or null
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Return all networks which have a reserved free IP.
     *
     * @return Network[]|null Array of network or null
     */
    public function getNetworkWithFreeIp()
    {
        //@FIXME
        return $this->repository->findAll();
    }

    /**
     * Return all networks which are not saturated.
     *
     * @return Network[]|null Array of network or null
     */
    public function getNonSaturatedNetwork()
    {
        //@FIXME
        return $this->repository->findAll();
    }

    /**
     * Retrieve logs of the axe.
     *
     * @param Network $entity
     *
     * @return array
     */
    public function retrieveLogs($entity): array
    {
        /** @var LogEntryRepository $logRepository */
        $logRepository = $this->em->getRepository(LogEntry::class); // we use default log entry class
        $logs = $logRepository->getLogEntries($entity);

        return LogFactory::createNetworkLogs($logs);
    }

    /**
     * Save new or modified Network.
     *
     * @param Network $network
     * @param User    $user
     */
    public function save(Network $network, User $user = null)
    {
        if ($user && empty($network->getCreator())) {
            $network->setCreator($user);
        }
        $this->em->persist($network);
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
            ->addSelect('COUNT(ips.id) AS ipsCount')
            ->leftjoin(self::ALIAS. '.site', 'site1')
            ->addSelect('IDENTITY(network.site) as HIDDEN site')
            ->groupBy(self::ALIAS.'.id');

        return $qb;
    }

    /**
     * This method will add the HIDDEN field, the sortable field.
     *
     * @see https://github.com/KnpLabs/KnpPaginatorBundle/issues/196
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     */
    protected function addHiddenField(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder
            ->addSelect('network.site.label as HIDDEN site.label');
    }
}
