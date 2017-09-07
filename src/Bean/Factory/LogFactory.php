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

namespace App\Bean\Factory;

use App\Bean\Log;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * Log bean to give some information about the last updates and the creation.
 *
 * @category Factory
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license GNU General Public License, version 3
 *
 * @see http://opensource.org/licenses/GPL-3.0
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
}
