<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.2
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
 * Interface InformationInterface.
 *
 * @category Entity
 */
interface ColorInterface
{
    /**
     * Return color in HTML.
     *
     * @return string | null
     */
    public function getColor(): string;

    /**
     * Return creator (user entity).
     *
     * @param string $color
     *
     * @return ColorInterface
     */
    public function setColor(string $color): self;

    /**
     * Return red value (0 to 255).
     *
     * @return int
     */
    public function getRed(): int;

    /**
     * Return green value (0 to 255).
     *
     * @return int
     */
    public function getGreen(): int;

    /**
     * Return blue value (0 to 255).
     *
     * @return int
     */
    public function getBlue(): int;
}
