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

namespace App\Entity;

/**
 * Interface ReferentInterface.
 *
 * @category App\Form\Type
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
interface ReferentInterface
{
    /**
     * Getter of the creator.
     *
     * @return User|null
     */
    public function getCreator(): ?User;

    /**
     * Setter of the Creator.
     *
     * @param User $creator
     *
     * @return ReferentTrait
     */
    public function setCreator(User $creator);
}
