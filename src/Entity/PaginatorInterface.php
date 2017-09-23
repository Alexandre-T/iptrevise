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

use Doctrine\ORM\QueryBuilder;

/**
 * Interface PaginatorInterface.
 *
 * @category App\Entity
 */
interface PaginatorInterface
{
    /**
     * Return the Query builder which is call by paginator.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder();
}
