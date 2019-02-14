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

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reserved ranges/Plages class.
 *
 * @category Entity
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @ORM\Entity(repositoryClass="App\Repository\PlageRepository")
 * @ORM\Table(name="te_plage")
 * @Gedmo\Loggable
 */
class Plage implements ColorInterface, InformationInterface, LabelInterface, ReferentInterface
{
    use ColorTrait;
    use ReferentTrait;

    /**
     * Internal identifier.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="plage_id", options={"comment":"Identifiant des plages"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Label of the range.
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="32")
     *
     * @ORM\Column(
     *     type="string",
     *     length=32,
     *     nullable=false,
     *     name="plage_lib",
     *     options={"comment":"Libellé de la plage réservée"}
     * )
     * @Gedmo\Versioned
     */
    private $label;

    /**
     * Starting IPv4.
     *
     * @var int
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(
     *     type="bigint",
     *     nullable=false,
     *     name="plage_start",
     *     options={"comment":"Adresse IPv4 de début de la plage réservée"}
     * )
     */
    private $start;

    /**
     * Ending IPv4.
     *
     * @var int
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(
     *     type="bigint",
     *     nullable=false,
     *     name="plage_end",
     *     options={"comment":"Adresse IPv4 de fin de la plage réservée"}
     * )
     */
    private $end;

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
     * Reason of the reservation range.
     *
     * @var string
     *
     * @Assert\Length(max="32")
     *
     * @ORM\Column(type="string", length=32, nullable=true, options={"comment":"Raison de la plage réservée"})
     * @Gedmo\Versioned
     */
    private $reason;

    /**
     * Network for this IP.
     *
     * @var Network
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Network", inversedBy="plages", fetch="EAGER")
     * @ORM\JoinColumn(name="network_id", referencedColumnName="net_id", nullable=false)
     * @Gedmo\Versioned
     */
    private $network;

    /**
     * Range color.
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^([0-9a-f]{3}|[0-9a-f]{6})$/i",
     *     message="form.site.error.color.pattern"
     * )
     *
     * @ORM\Column(
     *     type="string",
     *     length=6,
     *     nullable=false,
     *     name="pla_couleur",
     *     options={"default":000000,"comment":"Couleur ergonomique de la plage"}
     * )
     * @Gedmo\Versioned
     */
    private $color = '000000';

    /**
     * Get the identifier.
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
     * Get starting ip of range.
     *
     * @return int
     */
    public function getStart(): ?int
    {
        return $this->start;
    }

    /**
     * Get ending IP of range.
     *
     * @return int
     */
    public function getEnd(): ?int
    {
        return $this->end;
    }

    /**
     * Get reason of reservation.
     *
     * @return string
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * Get Network of range.
     *
     * @return Network
     */
    public function getNetwork(): ?Network
    {
        return $this->network;
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
     * Get last datetime update.
     *
     * @return DateTime
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Setter of the label.
     *
     * @param string $label
     *
     * @return Plage
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Setter of the first ip of the range.
     *
     * @param int $start
     *
     * @return Plage
     */
    public function setStart(int $start): self
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Setter of the last IP of the range.
     *
     * @param int $end
     *
     * @return Plage
     */
    public function setEnd(int $end): self
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Setter for the reservation reason.
     *
     * @param string $reason
     *
     * @return Plage
     */
    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Setter of the network.
     *
     * @param Network $network
     *
     * @return Plage
     */
    public function setNetwork(Network $network): self
    {
        $this->network = $network;

        return $this;
    }
}
