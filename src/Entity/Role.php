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
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Entity Rôle
 *
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 * @ORM\Table(name="tj_role", options={"comment":"Table entité des réseaux"})
 */
class Role
{
    /**
     * Kind of role.
     *
     * True : User can update data on this site.

     * False : User can only read data on this site.
     *
     * @var bool
     *
     * @ORM\Column(
     *     type="boolean",
     *     nullable=false,
     *     name="rol_lib",
     *     options={"comment":"lecteur = true, writer = false"}
     * )
     */
    private $readOnly = true;

    /**
     * Datetime creation (in the application) of the network.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="rol_created", options={"comment":"Creation datetime"})
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * Datetime last update (in the application) of the role.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="rol_updated", options={"comment":"Update datetime"})
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * Site authorized.
     *
     * var Site
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", fetch="EAGER", inversedBy="roles")
     * @ORM\JoinColumn(name="sit_id", referencedColumnName="sit_id", nullable=false, onDelete="CASCADE")
     * @ORM\Id
     */
    private $site;

    /**
     * User authorized.
     *

     * @var User
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="newRoles", cascade={"persist"})
     * @ORM\JoinColumn(name="usr_id", referencedColumnName="usr_id", nullable=false, onDelete="CASCADE") User
     */
    private $user;

    /**
     * Is it a readonly Role?
     *
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * Get datetime creation.
     *
     * @return DateTime
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Get datetime last update.
     *
     * @return DateTime
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Get the site.
     *
     * @return Site
     */
    public function getSite(): ?Site
    {
        return $this->site;
    }

    /**
     * Get the user.
     *
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set the authorization.
     *
     * @param bool $readOnly
     *
     * @return Role
     */
    public function setReadOnly(bool $readOnly): Role
    {
        $this->readOnly = $readOnly;

        return $this;
    }

    /**
     * Set the site.
     *
     * @param Site $site
     *
     * @return Role
     */
    public function setSite(Site $site): Role
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Set the user.
     *
     * @param User $user
     *
     * @return Role
     */
    public function setUser(User $user): Role
    {
        $this->user = $user;

        return $this;
    }
}
