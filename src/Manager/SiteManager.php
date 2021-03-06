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
use App\Entity\Site;
use App\Repository\SiteRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;

/**
 * Site Manager.
 *
 * @category Manager
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class SiteManager implements LoggableManagerInterface, PaginatorInterface
{
    /**
     * Const for the alias query.
     */
    const ALIAS = 'site';

    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Repository.
     *
     * @var SiteRepository
     */
    private $repository;

    /**
     * SiteManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->repository = $this->em->getRepository(Site::class);
    }

    /**
     * Return the number of sites registered in database.
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
     * Delete site without verification.
     *
     * @param Site $site
     */
    public function delete(Site $site)
    {
        $this->em->remove($site);
        $this->em->flush();
    }

    /**
     * Is this entity deletable?
     *
     * @param Site $site
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(Site $site): bool
    {
        //A site is deletable if there is no networks referenced.
        return 0 == count($site->getNetworks());
    }

    /**
     * Return all sites.
     *
     * @return Site[]|null Array of sites or null
     */
    public function getAll()
    {
        return $this->repository->findAll();

    }


    /**
     * Retrieve logs of the axe.
     *
     * @param Site $entity
     *
     * @return array
     */
    public function retrieveLogs($entity): array
    {
        /** @var LogEntryRepository $logRepository */
        $logRepository = $this->em->getRepository(LogEntry::class); // we use default log entry class
        $logs = $logRepository->getLogEntries($entity);

        return LogFactory::createSiteLogs($logs);
    }

    /**
     * Save new or modified Site.
     *
     * @param Site $site
     * @param User $user
     */
    public function save(Site $site, User $user = null)
    {
        if ($user && empty($site->getCreator())) {
            $site->setCreator($user);
        }
        $this->em->persist($site);
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

        $qb->leftJoin(self::ALIAS.'.networks', 'networks')
            ->addSelect('COUNT(networks.id) AS networksCount')
            ->groupBy(self::ALIAS.'.id');

        return $qb;
    }

    /**
     * Get Readable sites for user.
     *
     * @param User $user
     *
     * @return Site[]|Collection
     */
    public function getReadable(User $user) {
        if ($user->isAdmin()) {
            return $this->getAll();
        }

        $qb = $this->repository->createQueryBuilder(self::ALIAS);

        return $qb->join('site.roles', 'roles')
            ->where('roles.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get Readable sites for user.
     *
     * @param User $user
     *
     * @return Site[]|Collection
     */
    public function getEditable(User $user) {
        if ($user->isAdmin()) {
            return $this->getAll();
        }

        $qb = $this->repository->createQueryBuilder(self::ALIAS);

        return $qb->join('site.roles', 'roles')
            ->where('roles.user = :user')
            ->andWhere('roles.readOnly = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
