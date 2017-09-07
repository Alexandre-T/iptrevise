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
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="te_tag")
 * @Gedmo\Loggable
 */
class Tag
{
    /**
     * Identifier of Tag.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="tag_id", options={"unsigned":true,"comment":"Identifiant du tag"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Label of the tag.
     *
     * @var string
     *
     * @ORM\Column(
     *     type="string",
     *     unique=true,
     *     length=16,
     *     nullable=false,
     *     name="tag_lib",
     *     options={"comment":"Libellé du tag"}
     * )
     * @Gedmo\Versioned
     */
    private $label;

    /**
     * Datetime creation of the machine.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="mac_created", options={"comment":"Creation datetime"})
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * Last update datetime of the machine.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="mac_updated", options={"comment":"Update datetime"})
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * Machines attached to this Tag.
     *
     * @var Machine[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Machine", inversedBy="tags")
     * @ORM\JoinTable(
     *     name="tj_machinetag",
     *     joinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="tag_id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="machine_id", referencedColumnName="mac_id", nullable=false)}
     * )
     */
    private $machines;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->machines = new ArrayCollection();
    }

    /**
     * Get the internal identifier of this Tag.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the label of this Tag.
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Get the datetime creation of this tag.
     *
     * @return DateTime
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Get the datetime of the last update.

     * @return DateTime
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Get all machines.
     *
     * @return Collection
     */
    public function getMachines()
    {
        return $this->machines;
    }

    /**
     *
     * @param mixed $label
     * @return Tag
     */
    public function setLabel($label): Tag
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Add machine.
     *
     * @param Machine $machine
     *
     * @return Tag
     */
    public function addMachine(Machine $machine) :Tag
    {
        $this->machines[] = $machine;

        return $this;
    }
    /**
     * Remove machine.
     *
     * @param Machine $machine
     *
     * @return Tag
     */
    public function removeMachine(Machine $machine):Tag
    {
        $this->machines->removeElement($machine);

        return $this;
    }

    /**
     * This is a simple setters.
     *
     * @param mixed $machines
     * @return Tag
     */
    public function setMachines($machines): Tag
    {
        $this->machines = $machines;

        return $this;
    }
}
