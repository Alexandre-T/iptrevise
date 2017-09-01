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
 *
 */
namespace App\Listener;

use \Gedmo\Loggable\LoggableListener as GedmoLoggableListener;

/**
 * LoggableListener
 *
 * @author Alexandre Tranchant <alexandre.tranchant@gmail.com>
 */
class LoggableListener extends GedmoLoggableListener implements LoggableListenerInterface
{
}
