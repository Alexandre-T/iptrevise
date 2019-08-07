<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.2
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
 * Network class.
 *
 * @category Entity
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @ORM\Entity(repositoryClass="App\Repository\NetworkRepository")
 * @ORM\Table(name="te_network", options={"comment":"Table entité des réseaux"})
 * @Gedmo\Loggable
 *
 * @UniqueEntity("label", message="form.network.error.label.unique")
 */
class Network implements ColorInterface, InformationInterface, LabelInterface
{
    use ColorTrait;
    use ReferentTrait;
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
     * @Assert\NotBlank()
     * @Assert\Length(max="32")
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
     *
     *
     * @Gedmo\Versioned
     */
    private $ip;

    /**
     * Network cidr.
     *
     * @var int
     *
     * @Assert\Range(
     *     min = 0,
     *     max = 32,
     *     minMessage="form.network.error.cidr.min",
     *     maxMessage="form.network.error.cidr.max"
     * )
     *
     * @ORM\Column(
     *     type="smallint",
     *     nullable=false,
     *     name="net_masque",
     *     options={"default":32,"unsigned":true,"comment":"Masque du réseau"}
     * )
     * @Gedmo\Versioned
     */
    private $cidr;

    /**
     * Network color.
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^([0-9a-f]{3}|[0-9a-f]{6})$/i",
     *     message="form.network.error.color.pattern"
     * )
     *
     * @see https://stackoverflow.com/questions/9682709/regexp-matching-hex-color-syntax-and-shorten-form
     *
     * @ORM\Column(
     *     type="string",
     *     length=6,
     *     nullable=false,
     *     name="net_couleur",
     *     options={"default":000000,"comment":"Couleur ergonomique du réseau"}
     * )
     * @Gedmo\Versioned
     */
    private $color = '000000';

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
     * @ORM\OneToMany(targetEntity="App\Entity\Ip", mappedBy="network", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"ip" = "ASC"})
     *
     * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/tutorials/extra-lazy-associations.html
     */
    private $ips;

    /**
     * Reserved ranges of IP for this network.
     *
     * @var Collection|Plage[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Plage", mappedBy="network", fetch="EAGER")
     * @ORM\OrderBy({"start":"ASC"})
     */
    private $plages;

    /**
     * Network's site.
     *
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="networks", fetch="EAGER")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="sit_id", nullable=false, onDelete="RESTRICT")
     * @Gedmo\Versioned
     */
    private $site;

    /**
     * Network constructor.
     */
    public function __construct()
    {
        $this->ips = new ArrayCollection();
        $this->plages = new ArrayCollection();
    }

    /**
     * Return true when we can reserve an IP in this network.
     *
     * @return bool
     */
    public function hasSpace(): bool
    {
        //We can create an IP only if there is strictly more capacity than Ip counted.
        return $this->getCapacity() > $this->ips->count();
    }

    /**
     * Return the percent of occupied space.
     *
     * @return int
     */
    public function getPercent(): int
    {
        //We can create an IP only if there is strictly more capacity than Ip counted.
        return  (int) ($this->ips->count() / $this->getCapacity()) * 100;
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
     * Get the cidr of this network.
     *
     * @return int
     */
    public function getCidr(): ?int
    {
        return $this->cidr;
    }

    /**
     * Get reserved plage/range of the Network.
     *
     * @return Collection|Plage[]
     */
    public function getPlages(): Collection
    {
        return $this->plages;
    }

    /**
     * Return the site of this network.
     *
     * @return Site
     */
    public function getSite(): ?Site
    {
        return $this->site;
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
    public function setLabel(?string $label): self
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
    public function setDescription(?string $description): self
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
    public function setIp(int $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Set cidr of this network.
     *
     * @param int $cidr
     *
     * @return Network
     */
    public function setCidr(?int $cidr): self
    {
        $this->cidr = $cidr;

        return $this;
    }

    /**
     * Setter of the  site.
     *
     * @param Site $site
     *
     * @return Network
     */
    public function setSite(Site $site): self
    {
        $this->site = $site;

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
     * Add reserved plage/range.
     *
     * @param Plage $plage
     *
     * @return Network
     */
    public function addPlage(Plage $plage)
    {
        $this->plages[] = $plage;

        return $this;
    }

    /**
     * Remove plage.
     *
     * @param Plage $plage
     *
     * @return Network
     */
    public function removePlage(Plage $plage)
    {
        $this->plages->removeElement($plage);

        return $this;
    }

    /**
     * Set All Ip of this network.
     *
     * @param Collection $ips
     *
     * @return Network
     */
    public function setIps(Collection $ips): self
    {
        $this->ips = $ips;

        return $this;
    }

    /**
     * Count IPS.
     *
     * @return int
     */
    public function getIpCount(): int
    {
        return $this->ips->count();
    }

    /**
     * Calculate IP capacity of a network.
     *
     * @return int
     */
    public function getCapacity()
    {
        if (32 == $this->cidr) {
            return 1;
        } elseif (31 == $this->cidr) {
            return 2;
        }

        return pow(2, 32 - $this->cidr) - 2;
    }

    /**
     * Return the ip of the first machine of current network.
     *
     * @return int
     */
    public function getMinIp(): int
    {
        if (32 == $this->cidr || 31 == $this->cidr) {
            return $this->getIp();
        }

        return min($this->getIp() + 1, ip2long('255.255.255.255'));
    }

    /**
     * Return the last ip for a machine in current network.
     *
     * @return int
     */
    public function getMaxIp(): int
    {
        if (32 == $this->cidr) {
            return $this->getIp();
        } elseif (31 == $this->cidr) {
            return $this->getIp() + 1;
        }

        return min($this->getIp() + $this->getCapacity(), ip2long('255.255.255.254'));
    }

    /**
     * Return broadcast address (in long).
     *
     * @return int | null
     */
    public function getBroadcast(): ?int
    {
        if (32 == $this->cidr) {
            return $this->getIp();
        } elseif (31 == $this->cidr) {
            return null;
        }

        return min($this->getIp() + $this->getCapacity() + 1, ip2long('255.255.255.255'));
    }

    /**
     * Return broadcast address (in long).
     *
     * @return int
     */
    public function getMask(): int
    {
        return pow(2, 32) - pow(2, 32 - $this->cidr);
    }

    /**
     * Return wildcard address (in long).
     *
     * @return int
     */
    public function getWildcard(): int
    {
        return pow(2, 32 - $this->cidr) - 1;
    }
}
