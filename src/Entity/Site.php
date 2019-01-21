<?php
<<<<<<< HEAD
namespace App\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
=======
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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> upstream/master

/**
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 * @ORM\Table(name="te_site", options={"comment":"Table entité des réseaux"})
 * @Gedmo\Loggable
 */
class Site
{
    /**
     * Site id.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="sit_id", options={"unsigned":true,"comment":"Identifiant des machines"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Site label.
     *
     * @var string
     *
<<<<<<< HEAD
=======
     * @Assert\NotBlank()
     * @Assert\Length(max="32")
     *
>>>>>>> upstream/master
     * @ORM\Column(
     *     type="string",
     *     unique=true,
     *     length=32,
     *     nullable=false,
     *     name="sit_lib",
     *     options={"comment":"Libellé du réseau"}
     * )
     * @Gedmo\Versioned
     */
    private $label;

    /**
     * Site color
     *
     * @var string
     *
<<<<<<< HEAD
=======
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/^([0-9a-f]{3}|[0-9a-f]{6})$/i",
     *     message="form.site.error.color.pattern"
     * )
     *
>>>>>>> upstream/master
     * @ORM\Column(
     *     type="string",
     *     length=6,
     *     nullable=false,
     *     name="sit_couleur",
     *     options={"default":000000,"comment":"Couleur ergonomique du réseau"}
     * )
     * @Gedmo\Versioned
     */
    private $color;

    /**
     * Datetime creation (in the application) of the site.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="sit_created", options={"comment":"Creation datetime"})
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * Datetime of the last update (in the application) of the site.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="sit_updated", options={"comment":"Update datetime"})
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * NEtworks of this site.
     *
<<<<<<< HEAD
     * @var Network[]
=======
     * @var Network[]|Collection
>>>>>>> upstream/master
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Network", mappedBy="site", fetch="EXTRA_LAZY")
     */
    private $networks;

<<<<<<< HEAD
=======
    /**
     * Site constructor.
     */
    public function __construct()
    {
        $this->networks = new ArrayCollection();
    }

    /**
     * Get identifier.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get Label.
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * @return DateTime
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Get networks of the site.
     *
     * @return Network[]|Collection
     */
    public function getNetworks(): Collection
    {
        return $this->networks;
    }

    /**
     * Setter of label.
     *
     * @param string $label
     *
     * @return Site
     */
    public function setLabel(string $label): Site
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Setter of color.
     *
     * @param string $color
     *
     * @return Site
     */
    public function setColor(string $color): Site
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Add network.
     *
     * @param Network $network
     *
     * @return Site
     */
    public function addNetwork(Network $network): Site
    {
        $this->networks[] = $network;

        return $this;
    }

    /**
     * Remove network.
     *
     * @param Network $network
     *
     * @return Site
     */
    public function removeNetwork(Network $network): Site
    {
        $this->networks->removeElement($network);

        return $this;
    }
>>>>>>> upstream/master
}