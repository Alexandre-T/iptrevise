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

namespace App\Bean;

use DateTime as DateTime;

/**
 * Information bean to give some information about the last update and the creation.
 *
 * @category Bean
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class Information
{
    /**
     * @var DateTime Date and time creation of the entity
     */
    private $created;

    /**
     * @var string Mail of creator
     */
    private $creator;

    /**
     * @var DateTime Date and time update of the entity
     */
    private $updated;

    /**
     * Getter of the creation date time.
     *
     * @return DateTime | null
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Setter of the creation date time.
     *
     * @param DateTime $created
     *
     * @return Information
     */
    public function setCreated(DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Getter of the creation date time.
     *
     * @return string | null
     */
    public function getCreator(): ?string
    {
        return $this->creator;
    }

    /**
     * Setter of the creation date time.
     *
     * @param string $creator
     *
     * @return Information
     */
    public function setCreator(string $creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Getter of update time.
     *
     * @return DateTime | null
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Setter of update time.
     *
     * @param DateTime $updated
     *
     * @return Information
     */
    public function setUpdated(DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Has the creator been initialized?
     *
     * @return bool
     */
    public function hasCreated(): bool
    {
        return !empty($this->creator);
    }

    /**
     * Has the created been initialized?
     *
     * @return bool
     */
    public function isCreated(): bool
    {
        return !empty($this->created);
    }

    /**
     * Has the updated been initialized?
     *
     * @return bool
     */
    public function isUpdated(): bool
    {
        return !empty($this->updated);
    }
}
