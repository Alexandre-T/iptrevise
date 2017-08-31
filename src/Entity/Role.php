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
 *
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;

/**
 * Role entity.
 *
 * @ORM\Entity
 * @ORM\Table(name="ts_role", options={"comment":"Table entité des utilisateur"})
 * @Gedmo\Loggable
 */
class Role
{
    /**
     * Role Identifiant.
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="rol_id", options={"unsigned":true,"comment":"Identifiant du rôle"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Unique code of the role.
     *
     * @var string
     *
     * @ORM\Column(type="string", unique=true, length=16, nullable=false, options={"comment":"Code of this role"})
     */
    private $code;

    /**
     * Label of the role.
     *
     * @var string
     *
     * @ORM\Column(
     *     type="string",
     *     unique=true,
     *     length=32,
     *     nullable=false,
     *     name="rol_label",
     *     options={"unsigned":true,"comment":"Label du rôle"}
     * )
     * @Gedmo\Versioned
     */
    private $label;

    /**
     * Creation datetime.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="rol_created", options={"comment":"Creation datetime"})
     */
    private $created;

    /**
     * Update datetime.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="rol_updated", options={"comment":"Update datetime"})
     */
    private $updated;

    /**
     * Users of this role.
     *
     * @var User[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="roles")
     */
    private $users;

    /**
     * Role constructor.
     *
     * @constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * Getter Role ID.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Code getter.
     *
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Label Getter.
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Creation datetime getter.
     *
     * @return DateTime
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Last update datetime.
     *
     * @return DateTime
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Get users of this role.
     *
     * @return ArrayCollection[User]
     */
    public function getUsers(): ArrayCollection
    {
        return $this->users;
    }

    /**
     * Setter of the code.
     *
     * @param mixed $code
     * @return Role
     */
    public function setCode($code): Role
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Setter of the label.
     *
     * @param string $label
     * @return Role
     */
    public function setLabel(string $label): Role
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Setter of the users.
     *
     * @param User[] $users
     * @return Role
     */
    public function setUsers(array $users): Role
    {
        $this->users = $users;
        return $this;
    }
}
