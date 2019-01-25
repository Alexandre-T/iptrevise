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

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * IpTransformer class.
 *
 * @category App\Form\DataTransformer
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class ColorTransformer implements DataTransformerInterface
{
    /**
     * Transform Long into IP.
     *
     * @param string $color
     *
     * @return string|null
     */
    public function transform($color)
    {
        if (is_null($color)) {
            return null;
        }

        return "#$color";
    }

    /**
     * Transform IP to long.
     *
     * @param string $color
     *
     * @return string
     */
    public function reverseTransform($color)
    {
        if (empty($color)) {
            return null;
        }

        return substr($color, 1);
    }
}
