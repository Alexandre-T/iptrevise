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

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;

/**
 * Network class.
 *
 * @category Entity
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 * @ORM\Entity(repositoryClass="App\Repository\NetworkRepository")
 * @ORM\Table(name="te_network", options={"comment":"Table entité des réseaux"})
 * @Gedmo\Loggable
 */
class Network
{
    /**
     * Internal identifier.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="net_id", options={"unsigned":true,"comment":"Identifiant des machines"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Label of the Network.
     *
     * @var string
     *
     * @ORM\Column(
     *     type="string",
     *     unique=true,
     *     length=32,
     *     nullable=false,
     *     name="net_lib",
     *     options={"comment":"Libellé du réseau"}
     * )
     * @Gedmo\Versioned
     */
    private $label;

    /**
     * Description of the network.
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true, name="net_des", options={"comment":"Description du réseau"})
     * @Gedmo\Versioned
     */
    private $description;

    /**
     * Address of the network.
     *
     * @var int
     *
     * @ORM\Column(type="bigint", nullable=false, name="net_ip", options={"unsigned":true,"comment":"Adresse Réseau IPv4"})
     * @Gedmo\Versioned
     */
    private $ip;

    /**
     * Network mask.
     *
     * @var int
     *
     * @ORM\Column(
     *     type="smallint",
     *     nullable=false,
     *     name="net_masque",
     *     options={"unsigned":true,"comment":"Masque du réseau"}
     * )
     * @Gedmo\Versioned
     */
    private $mask;

    /**
     * Network color.
     *
     * @var string
     *
     * @ORM\Column(
     *     type="string",
     *     unique=true,
     *     length=6,
     *     nullable=false,
     *     name="net_couleur",
     *     options={"comment":"Couleur ergonomique du réseau"}
     * )
     * @Gedmo\Versioned
     */
    private $color;

    /**
     * Datetime creation (in the application) of the network.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="net_created", options={"comment":"Creation datetime"})
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * Datetime last update (in the application) of the network.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="net_updated", options={"comment":"Update datetime"})
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * List of all referenced IP in this network.
     *
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Ip", mappedBy="network")
     */
    private $ips;

    /**
     * Network constructor.
     */
    public function __construct()
    {
        $this->ips = new ArrayCollection();
    }

    /**
     * Get the internal identifier.
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
     * Get the adress of the network.
     *
     * @return int
     */
    public function getIp(): ?int
    {
        return $this->ip;
    }

    /**
     * Get the mask of this network.
     *
     * @return int
     */
    public function getMask(): ?int
    {
        return $this->mask;
    }

    /**
     * Get the color of this network.
     *
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * Get the datetime creation of this network.
     *
     * @return DateTime
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Get the datetime last update.
     *
     * @return DateTime
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Get all referenced IP for this network.
     *
     * @return Collection
     */
    public function getIps(): ?Collection
    {
        return $this->ips;
    }

    /**
     * Set the label of network.
     *
     * @param string $label
     *
     * @return Network
     */
    public function setLabel(string $label): Network
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the description of the network.
     *
     * @param string $description
     *
     * @return Network
     */
    public function setDescription(string $description): Network
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set IP Address of network.
     *
     * @param int $ip
     *
     * @return Network
     */
    public function setIp(int $ip): Network
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Set mask of this network.
     *
     * @param int $mask
     *
     * @return Network
     */
    public function setMask(int $mask): Network
    {
        $this->mask = $mask;

        return $this;
    }

    /**
     * Set Color of this network.
     *
     * @param string $color
     *
     * @return Network
     */
    public function setColor(string $color): Network
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Add ip.
     *
     * @param Ip $ip
     *
     * @return Network
     */
    public function addIp(Ip $ip)
    {
        $this->ips[] = $ip;

        return $this;
    }

    /**
     * Remove ip.
     *
     * @param Ip $ip
     *
     * @return Network
     */
    public function removeIp(Ip $ip)
    {
        $this->ips->removeElement($ip);

        return $this;
    }

    /**
     * Set All Ip of this network.
     *
     * @param Collection $ips
     *
     * @return Network
     */
    public function setIps(Collection $ips): Network
    {
        $this->ips = $ips;

        return $this;
    }
}
