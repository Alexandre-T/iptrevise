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

namespace App\Listener;

/**
 * LoggableListener.
 *
 * @author Alexandre Tranchant <alexandre.tranchant@gmail.com>
 */
interface LoggableListenerInterface
{
    /**
     * Set the username of connected user doing an update.
     *
     * @param string
     *
     * @return mixed
     */
    public function setUsername($username);
}
