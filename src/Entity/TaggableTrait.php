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
 * Taggable Trait.
 *
 * @category App\Entity
 */
trait TaggableTrait
{
    /**
     * Add tag.
     *
     * @param Tag $tag
     *
     * @return TaggableInterface
     */
    public function addTag(Tag $tag): TaggableInterface
    {
        $this->tags->add($tag);

        return $this;
    }

    /**
     * Return a collection of all tags.
     *
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Remove tag.
     *
     * @param Tag $tag
     *
     * @return TaggableInterface
     */
    public function removeTag(Tag $tag): TaggableInterface
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
