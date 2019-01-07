<?php
namespace App\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Entity Rôle
 *
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 * @ORM\Table(name="tj_role", options={"comment":"Table entité des réseaux"})
 */
class Role
{
    /**
     * Kind of role.
     *
     * True : User can update data on this site.
     * False : User can only read data on this site.
     *
     * @ORM\Column(
     *     type="boolean",
     *     unique=true,
     *     nullable=false,
     *     name="rol_lib",
     *     options={"comment":"lecteur = true, writer = false"}
     * )
     */
    private $readOnly = true;

    /**
     * Datetime creation (in the application) of the network.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="rol_created", options={"comment":"Creation datetime"})
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * Datetime last update (in the application) of the role.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="rol_updated", options={"comment":"Update datetime"})
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * Site authorized.
     *
     * @var Site
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", fetch="EAGER")
     * @ORM\JoinColumn(name="sit_id", referencedColumnName="sit_id", nullable=false, onDelete="CASCADE")
     * @ORM\Id
     */
    private $site;

    /**
     * User authorized.
     *
     * @var
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="newRoles")
     * @ORM\JoinColumn(name="usr_id", referencedColumnName="usr_id", nullable=false, onDelete="CASCADE") User
     */
     private $user;

     public function __construct()
     {
     }
     /**
     * returns true if the user can only read
     * @return boolean
     */
     public function isReadOnly() : boolean
     {
       return $this->readOnly;
     }
     /**
     * Get the datetime creation (in application) of this role.
     *
     * @return DateTime
     */
     public function getCreated(): ?DateTime
     {
       return $this->created;
     }

     /**
     * Get the last datetime update (in application) of this role.
     *
     * @return mixed
     */
     public function getUpdated(): ?DateTime
     {
       return $this->updated;
     }
     /**
     * Get the site authorized to this role.
     *
     * @return Site
     */
     public function getSite(): ?Site
     {
       return $this->site;
     }
     /**
     * TODO getter for attribut $user?
     * Role constructor.
     */
   }
