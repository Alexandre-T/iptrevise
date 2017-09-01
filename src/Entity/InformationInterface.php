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
namespace App\Entity;

use DateTime;

/**
 * Interface InformationInterface
 *
 * @category Entity
 *
 * @package AppBundle\Entity
 */
interface InformationInterface
{
    /**
     * Return date time creation.
     *
     * @return dateTime | null
     */
    public function getCreated(): ?DateTime;

    /**
     * Return date time of the last update.
     *
     * @return dateTime | null
     */
    public function getUpdated(): ?DateTime;
}
