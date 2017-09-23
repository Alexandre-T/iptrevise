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

use App\Bean\Information;
use App\Entity\InformationInterface;
use App\Entity\User;

/**
 * Information bean to give some information about the last update and the creation.
 *
 * @category Factory
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class InformationFactory
{
    /**
     * Create Information from a Family Entity.
     *
     * @param InformationInterface $entity
     *
     * @return Information
     */
    public static function createInformation(InformationInterface $entity): Information
    {
        $information = new Information();
        $information->setCreated($entity->getCreated());

        if ($entity->getUpdated()) {
            $information->setUpdated($entity->getUpdated());
        }

        if ($entity->getCreator() instanceof User) {
            $information->setCreator($entity->getCreator()->getUsername());
        }

        return $information;
    }
}
