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
 * Trait ColorTrait.
 *
 * @category Entity
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @property string color;
 */
trait ColorTrait
{
    /**
     * Get the color.
     *
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Get the color.
     *
     * @param string $color
     *
     * @return self
     */
    public function setColor(string $color): ColorInterface
    {
        if (3 === strlen($color)) {
            $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
        }

        $this->color = $color;

        return $this;
    }

    /**
     * Get the red color.
     *
     * @return int
     */
    public function getRed(): int
    {
        if (!$this->isValid()) {
            return 0;
        }

        return hexdec(substr($this->color, 0, 2));
    }

    /**
     * Get the blue color.
     *
     * @return int
     */
    public function getBlue(): int
    {
        if (!$this->isValid()) {
            return 0;
        }

        return hexdec(substr($this->color, 4, 2));
    }

    /**
     * Get the green color.
     *
     * @return int
     */
    public function getGreen(): int
    {
        if (!$this->isValid()) {
            return 0;
        }

        return hexdec(substr($this->color, 2, 2));
    }

    /**
     * Is color a valid RVB string?
     *
     * @return bool
     */
    private function isValid(): bool
    {
        return 1 === preg_match('/^([0-9a-f]{3}|[0-9a-f]{6})$/i', $this->color);
    }
}
