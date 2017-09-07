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
 * @copyright 2017 Cerema — Alexandre Tranchant
 * @license   Propriétaire Cerema
 */

namespace App\Bean;

use DateTime as DateTime;

/**
 * Information bean to give some information about the last update and the creation.
 *
 * @category Bean
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license GNU General Public License, version 3
 *
 * @see http://opensource.org/licenses/GPL-3.0
 */
class Information
{
    /**
     * @var DateTime Date and time creation of the entity
     */
    private $created;

    /**
     * @var DateTime Date and time creation of the entity
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
