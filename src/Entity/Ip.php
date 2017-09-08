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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;

/**
 * Ip class.
 *
 * @category App\Entity
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 * @ORM\Entity(repositoryClass="App\Repository\IpRepository")
 * @ORM\Table(name="te_ip")
 * @Gedmo\Loggable
 */
class Ip
{
    /**
     * IP Identifient.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="ip_id", options={"comment":"Identifiant des adresses IP"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * IP label.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false, name="ip_lib", options={"comment":"Libellé de l'adresse IP"})
     * @Gedmo\Versioned
     */
    private $label;

    /**
     * IP Description.
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true, name="ip_des", options={"comment":"Description de l'adresse IP"})
     * @Gedmo\Versioned
     */
    private $description;

    /**
     * IPv4.
     *
     * @var int
     *
     * @ORM\Column(type="bigint", nullable=false, name="ip_ip", options={"comment":"Adresse IPv4"})
     * @Gedmo\Versioned
     */
    private $ip;

    /**
     * Datetime creation of IP.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="ip_created", options={"comment":"Creation datetime"})
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * Last update datetime.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="ip_updated", options={"comment":"Update datetime"})
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * Network for this IP.
     *
     * @var Network
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Network", inversedBy="ips", fetch="EAGER")
     * @ORM\JoinColumn(name="network_id", referencedColumnName="net_id", nullable=false)
     * @Gedmo\Versioned
     */
    private $network;

    /**
     * Machine of this IP.
     *
     * @var Machine
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Machine", inversedBy="ips", fetch="EAGER")
     * @ORM\JoinColumn(name="machine_id", referencedColumnName="mac_id")
     * @Gedmo\Versioned
     */
    private $machine;

    /**
     * Get the internal identifient.
     *
     * @return null|int
     */
    public function getId(): ?integer
    {
        return $this->id;
    }

    /**
     * Get the label of IP.
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Get the Description of IP.
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get the IP.
     *
     * @return int
     */
    public function getIp(): ?int
    {
        return $this->ip;
    }

    /**
     * Get the datetime creation of this IP.
     *
     * @return DateTime
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Get the last update of this IP.
     *
     * @return DateTime
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Get the Network of this IP.
     *
     * @return Network
     */
    public function getNetwork(): ?Network
    {
        return $this->network;
    }

    /**
     * Get the machine affected by this IP.
     *
     * @return Machine
     */
    public function getMachine(): ?Machine
    {
        return $this->machine;
    }

    /**
     * Set the label of IP.
     *
     * @param string $label
     *
     * @return Ip
     */
    public function setLabel(string $label): Ip
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the description of this IP.
     *
     * @param string $description
     *
     * @return Ip
     */
    public function setDescription(string $description): Ip
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the IP of this entity.
     *
     * @param int $ip
     *
     * @return Ip
     */
    public function setIp(int $ip): Ip
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Set the Network of this IP.
     *
     * @param Network $network
     *
     * @return Ip
     */
    public function setNetwork(Network $network): Ip
    {
        $this->network = $network;

        return $this;
    }

    /**
     * Set the Machine of this IP.
     *
     * @param Machine $machine
     *
     * @return Ip
     */
    public function setMachine(Machine $machine): Ip
    {
        $this->machine = $machine;

        return $this;
    }
}
