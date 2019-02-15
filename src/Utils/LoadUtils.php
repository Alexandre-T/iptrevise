<?php
/**
 * This file is part of the LAPI application.
 *
 * PHP version 7.2
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license   MIT
 *
 * @see https://github.com/Alexandre-T/casguard/blob/master/LICENSE
 */

namespace App\Utils;

/**
 * Outils pour faciliter le chargement en base de données.
 */
class LoadUtils
{
    /**
     * Compte le nombre de lignes d'un fichier csv transmis.
     *
     * @param \SplFileObject $fileObject
     *
     * @return int
     */
    public function getLines(\SplFileObject $fileObject): int
    {
        $fileObject->seek(PHP_INT_MAX);
        $lignes = $fileObject->key();
        $fileObject->rewind();

        return $lignes;
    }
}
