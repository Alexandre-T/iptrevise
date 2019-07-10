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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
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
class DeletedSiteManager implements LoggableManagerInterface, PaginatorInterface
{
    /**
     * Const for the alias query.
     */
    const ALIAS = 'ext_log_entries';

    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Repository.
     *
     * @var LogEntryRepository
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
        $this->repository = $this->em->getRepository(LogEntry::class);
    }

    /**
     * Return all log entries.
     *
     * @return LogEntry[]|null Array of log entries or null
     */
    public function getAll()
    {
        return $this->repository->findAll();

    }

    public function getOtherLogEntries($log)
    {
         $q = $this->getOtherLogEntriesQuery($log);

         return $q->getResult();
    }

    /**
     * @param LogEntry $log
     *
     * @return Query
     */
    public function getOtherLogEntriesQuery($log)
    {

      $qb = $this->em->createQuery('SELECT log FROM Gedmo\Loggable\Entity\LogEntry log WHERE log.objectId = ?1 AND log.objectClass = \'App\Entity\Site\' ORDER BY log.version DESC')
      ->setParameter(1, $log->getObjectId());

      return $qb;
     }


    /**
     * Retrieve logs of the axe.
     *
     * @param LogEntry $log
     *
     * @return array
     */
    public function retrieveLogs($log): array
    {
        $logs = $this->getOtherLogEntries($log);

        return LogFactory::createSiteLogs($logs);
    }

    /**
     * Return the Query builder needed by the paginator.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
      //$qb = $this->em->createQuery('SELECT log.objectId FROM Gedmo\Loggable\Entity\LogEntry log WHERE log.action = \'remove\'');

        $qb = $this->repository->createQueryBuilder(self::ALIAS);

        $qb->where(self::ALIAS.'.objectClass = \'App\Entity\Site\'')
            ->andWhere(self::ALIAS.'.action = \'create\'')
            ->andWhere(self::ALIAS.'.objectId IN (SELECT log.objectId FROM Gedmo\Loggable\Entity\LogEntry log WHERE log.action = \'remove\' AND log.objectClass = \'App\Entity\Site\')');
            //->setParameter('query', $qb);

        return $qb;
    }
}
