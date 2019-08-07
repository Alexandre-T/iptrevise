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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Machine class.
 *
 * @category App\Entity
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @ORM\Entity(repositoryClass="App\Repository\MachineRepository")
 * @ORM\Table(name="te_machine", options={"comment":"Table entité des machines"})
 * @Gedmo\Loggable
 *
 * @UniqueEntity("label", message="form.machine.error.label.unique")
 * @UniqueEntity("macs", message="form.machine.error.macs.unique")
 */
class Machine implements InformationInterface, LabelInterface, TaggableInterface
{
    use ReferentTrait;
    use TaggableTrait;

    /**
     * Identifier of Machine.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="mac_id", options={"unsigned":true,"comment":"Identifiant des machines"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Label of Machine.
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="64")
     *
     * @ORM\Column(
     *     type="string",
     *     unique=true,
     *     length=64,
     *     nullable=false,
     *     name="mac_lib",
     *     options={"comment":"Libellé de la machine"}
     * )
     * @Gedmo\Versioned
     */
    private $label;

    /**
     * Description of machine.
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true, name="mac_des", options={"comment":"Description de la machine"})
     * @Gedmo\Versioned
     */
    private $description;

    /**
     * Number of network interface of Machine.
     *
     * @var int
     *
     * @Assert\GreaterThanOrEqual(value="0", message="form.machine.error.interface.min")
     *
     * @ORM\Column(
     *     type="smallint",
     *     nullable=false,
     *     name="mac_interface",
     *     options={"default":1,"unsigned":true,"comment":"Nombre d'interface réseau de la machine"}
     * )
     * @Gedmo\Versioned
     */
    private $interface = 1;

    /**
     * Datetime creation (in application) of Machine.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="mac_created", options={"comment":"Creation datetime"})
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * Last datetime update of machine.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="mac_updated", options={"comment":"Update datetime"})
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;
    /**
     * Machine location.
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true, name="mac_location", options={"comment":"Localisation  de la machine"})
     */
    private $location;

    /**
     * Adresses Mac of the Machine.
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true, name="mac_macs", options={"comment":"Adresses mac de la machine"})
     */
    private $macs;
    /**
     * Ip of this Machine.
     *
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Ip", mappedBy="machine", fetch="EAGER")
     * @ORM\OrderBy({"ip" = "ASC"})
     */
    private $ips;

    /**
     * Machine's tags.
     *
     * @var Tag[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="tj_machinetag",
     *     joinColumns={@ORM\JoinColumn(name="machine_id", referencedColumnName="mac_id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="tag_id", nullable=false)}
     * )
     */
    private $tags;

    /**
     * Services done by machine.
     *
     * @var Service[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Service", inversedBy="machines")
     * @ORM\JoinTable(
     *     name="tj_machineservice",
     *     joinColumns={@ORM\JoinColumn(name="machine_id", referencedColumnName="mac_id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="service_id", referencedColumnName="ser_id", nullable=false)}
     * )
     */
    private $services;

    /**
     * Machine constructor.
     */
    public function __construct()
    {
        $this->ips = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    /**
     * Get Identifier.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the label.
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Get the description.
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get the location of the machine.
     *
     * @return string
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * Get the macs adresses of the machine.
     *
     * @return string
     */
    public function getMacs(): ?string
    {
        return $this->macs;
    }

    /**
     * Get the number of interfaces.
     *
     * @return int
     */
    public function getInterface(): ?int
    {
        return $this->interface;
    }

    /**
     * Get the datetime creation (in application) of this machine.
     *
     * @return DateTime
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Get the last datetime update (in application) of this machine.
     *
     * @return mixed
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Return a collection of all Ips.
     *
     * @return Collection
     */
    public function getIps(): Collection
    {
        return $this->ips;
    }

    /**
     * Return a collection of all Services.
     *
     * @return Collection
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    /**
     * Set the label.
     *
     * @param string $label
     *
     * @return Machine
     */
    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the description.
     *
     * @param string $description
     *
     * @return Machine
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the number of network interface of this machine.
     *
     * @param int $interface
     *
     * @return Machine
     */
    public function setInterface(?int $interface): self
    {
        $this->interface = $interface;

        return $this;
    }

    /**
     * Set the location.
     *
     * @param string $location
     *
     * @return Machine
     */
    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Set the macs.
     *
     * @param string $macs
     *
     * @return Machine
     */
    public function setMacs(?string $macs): self
    {
        $this->macs = $macs;

        return $this;
    }

    /**
     * Add ip.
     *
     * @param Ip $ip
     *
     * @return Machine
     */
    public function addIp(Ip $ip): self
    {
        $this->ips[] = $ip;

        return $this;
    }

    /**
     * Remove ip.
     *
     * @param Ip $ip
     *
     * @return Machine
     */
    public function removeIp(Ip $ip): self
    {
        $this->ips->removeElement($ip);

        return $this;
    }

    /**
     * Add service.
     *
     * @param Service $service
     *
     * @return Machine
     */
    public function addService(Service $service): self
    {
        $this->services->add($service);

        return $this;
    }

    /**
     * Remove service.
     *
     * @param Service $service
     *
     * @return Machine
     */
    public function removeService(Service $service): self
    {
        $this->services->removeElement($service);

        return $this;
    }
}
