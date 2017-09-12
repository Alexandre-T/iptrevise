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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


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
 *
 * @UniqueEntity(fields={"ip", "network"}, message="form.ip.error.ip.unique")
 */
class Ip implements InformationInterface, ReferentInterface
{
    use ReferentTrait;
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
     * IPv4.
     *
     * @var int
     *
     * @Assert\NotBlank()
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
     * Reason of the reservation IP.
     *
     * @var string
     *
     * @Assert\Length(max="32")
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     * @Gedmo\Versioned
     */
    private $reason;
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
    public function getId(): ?int
    {
        return $this->id;
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
     * Get the reason of the reservation.
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
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
    public function setMachine(?Machine $machine): Ip
    {
        $this->machine = $machine;

        return $this;
    }

    /**
     * Set the reason of the reservation
     *
     * @param string $reason
     * @return Ip
     */
    public function setReason(?string $reason): Ip
    {
        $this->reason = $reason;
        return $this;
    }

}
