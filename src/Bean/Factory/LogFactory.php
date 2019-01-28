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

namespace App\Bean\Factory;

use App\Bean\Log;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * Log bean to give some information about the last updates and the creation.
 *
 * @category Factory
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class LogFactory
{
    /**
     * Create Log bean from a Abstract Log Entry (Gedmo).
     *
     * @param AbstractLogEntry[] $logEntries of AbstractLogEntry $logEntry
     *
     * @return Log[]
     */
    public static function createUserLogs(array $logEntries): array
    {
        $logs = [];
        foreach ($logEntries as $logEntry) {
            $logBean = new Log();
            $logBean->setAction('administration.log.action.'.$logEntry->getAction());
            $logBean->setLogged($logEntry->getLoggedAt());
            $logBean->setUsername($logEntry->getUsername());
            $logBean->setVersion($logEntry->getVersion());
            $logBean->setData(DataFactory::createUserData($logEntry->getData()));
            $logs[] = $logBean;
        }

        return $logs;
    }

    /**
     * Create Log bean from a Abstract Log Entry (Gedmo).
     *
     * @param AbstractLogEntry[] $logEntries of AbstractLogEntry $logEntry
     *
     * @return Log[]
     */
    public static function createNetworkLogs(array $logEntries): array
    {
        $logs = [];
        foreach ($logEntries as $logEntry) {
            $logBean = new Log();
            $logBean->setAction('administration.log.action.'.$logEntry->getAction());
            $logBean->setLogged($logEntry->getLoggedAt());
            $logBean->setUsername($logEntry->getUsername());
            $logBean->setVersion($logEntry->getVersion());
            $logBean->setData(DataFactory::createNetworkData($logEntry->getData()));
            $logs[] = $logBean;
        }

        return $logs;
    }

    /**
     * Create Log bean from a Abstract Log Entry (Gedmo).
     *
     * @param AbstractLogEntry[] $logEntries of AbstractLogEntry $logEntry
     *
     * @return Log[]
     */
    public static function createMachineLogs(array $logEntries): array
    {
        $logs = [];
        foreach ($logEntries as $logEntry) {
            $logBean = new Log();
            $logBean->setAction('administration.log.action.'.$logEntry->getAction());
            $logBean->setLogged($logEntry->getLoggedAt());
            $logBean->setUsername($logEntry->getUsername());
            $logBean->setVersion($logEntry->getVersion());
            $logBean->setData(DataFactory::createMachineData($logEntry->getData()));
            $logs[] = $logBean;
        }

        return $logs;
    }

    /**
     * Create Log bean from a Abstract Log Entry (Gedmo).
     *
     * @param AbstractLogEntry[] $logEntries of AbstractLogEntry $logEntry
     *
     * @return Log[]
     */
    public static function createIpLogs(array $logEntries): array
    {
        $logs = [];
        foreach ($logEntries as $logEntry) {
            $logBean = new Log();
            $logBean->setAction('administration.log.action.'.$logEntry->getAction());
            $logBean->setLogged($logEntry->getLoggedAt());
            $logBean->setUsername($logEntry->getUsername());
            $logBean->setVersion($logEntry->getVersion());
            $logBean->setData(DataFactory::createIpData($logEntry->getData()));
            $logs[] = $logBean;
        }

        return $logs;
    }

    /**
     * Create Log bean from a Abstract Log Entry (Gedmo).
     *
     * @param AbstractLogEntry[] $logEntries of AbstractLogEntry $logEntry
     *
     * @return Log[]
     */
    public static function createSiteLogs(array $logEntries): array
    {
        $logs = [];
        foreach ($logEntries as $logEntry) {
            $logBean = new Log();
            $logBean->setAction('administration.log.action.'.$logEntry->getAction());
            $logBean->setLogged($logEntry->getLoggedAt());
            $logBean->setUsername($logEntry->getUsername());
            $logBean->setVersion($logEntry->getVersion());
            $logBean->setData(DataFactory::createSiteData($logEntry->getData()));
            $logs[] = $logBean;
        }

        return $logs;
    }

    /**
     * Create Log bean from a Abstract Log Entry (Gedmo).
     *
     * @param AbstractLogEntry[] $logEntries of AbstractLogEntry $logEntry
     *
     * @return Log[]
     */
    public static function createServiceLogs(array $logEntries): array
    {
        $logs = [];
        foreach ($logEntries as $logEntry) {
            $logBean = new Log();
            $logBean->setAction('administration.log.action.'.$logEntry->getAction());
            $logBean->setLogged($logEntry->getLoggedAt());
            $logBean->setUsername($logEntry->getUsername());
            $logBean->setVersion($logEntry->getVersion());
            $logBean->setData(DataFactory::createServiceData($logEntry->getData()));
            $logs[] = $logBean;
        }

        return $logs;
    }
}
