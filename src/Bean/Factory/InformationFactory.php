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
namespace App\Bean\Factory;

use App\Bean\Information;
use App\Entity\InformationInterface;

/**
 * Information bean to give some information about the last update and the creation.
 *
 * @category Factory
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license GNU General Public License, version 3
 *
 * @link http://opensource.org/licenses/GPL-3.0
 *
 */
class InformationFactory
{
    /**
     * Create Information from a Family Entity.
     *
     * @param InformationInterface $entity
     * @return Information
     */
    public static function createInformation(InformationInterface $entity):Information
    {
        $information = new Information();
        $information->setCreated($entity->getCreated());

        if ($entity->getUpdated()) {
            $information->setUpdated($entity->getUpdated());
        }

        return $information;
    }
}
