<?php
namespace App\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * @var Network[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Network", mappedBy="site", fetch="EXTRA_LAZY")
     */
    private $networks;

}