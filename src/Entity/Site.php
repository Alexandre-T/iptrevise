<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 * @ORM\Table(name="te_site", options={"comment":"Table entité des sites"})
 * @Gedmo\Loggable
 *
 * @UniqueEntity("label", message="form.site.error.label.unique")
 */
class Site implements InformationInterface, LabelInterface
{
    //use ReferentTrait;
    /**
     * Site id.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="sit_id", options={"unsigned":true,"comment":"Identifiant du site"})
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
     * Networks of this site.
     *
     * @var Network[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Network", mappedBy="site", fetch="EXTRA_LAZY")
     */
    private $networks;

    /**
     * Get the newtorks of the site
     *
     * @return Network[]
     *
     */
    public function getNetworks() : ?Collection
    {
        return $this->networks;
    }

    /**
    * Get the name of the site
    *
    * @return string
    */
    public function getLabel() : ?string
    {
      return $this->label;
    }

    public function getColor() : ?string
    {
      return $this->color;
    }

    public function getId() : int
    {
      return $this->id;
    }


    public function setLabel(string $label): Site
    {
      $this->label = $label;
      return $this;
    }

    public function setColor(string $color): Site
    {
      $this->color = $color;
      return $this;
    }

    public function setNetworks(array $networks): Site
    {
      $this->networks = $networks;
      return $this;
    }

    public function getCreated(): ?DateTime
    {
      return $this->created;
    }

    public function getUpdated(): ?Datetime
    {
      return $this->updated;
    }

    public function getCreator(): ?User
    {
      $user = new User();
      $user->setUsername("user");
      return $user;
    }

}
