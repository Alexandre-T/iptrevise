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

use Doctrine\Common\Collections\Collection;

/**
 * Taggable Interface.
 *
 * An entity which implements this interface can be associated with tags.
 *
 * @category Entity
 */
interface TaggableInterface
{
    /**
     * Add a tag to this entity.
     *
     * @param Tag $tag
     *
     * @return TaggableInterface
     */
    public function addTag(Tag $tag): TaggableInterface;

    /**
     * Return the tags of this entity.
     *
     * @return Collection
     */
    public function getTags(): Collection;

    /**
     * Remove a tag from this entity.
     *
     * @param Tag $tag
     *
     * @return TaggableInterface
     */
    public function removeTag(Tag $tag): TaggableInterface;
}
