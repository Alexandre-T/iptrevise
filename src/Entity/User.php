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
 *
 */
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use Serializable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entity User.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(
 *     name="ts_user",
 *     options={"comment":"Table entité des utilisateur","charset":"utf8mb4","collate":"utf8mb4_unicode_ci"}
 * )
 * @Gedmo\Loggable
 */
class User implements InformationInterface, UserInterface, Serializable
{
    /**
     * Identifiant.
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="usr_id", options={"unsigned":true,"comment":"Identifiant de l'utilisateur"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Username.
     *
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", unique=true, length=32, nullable=false, name="usr_label", options={"unsigned":true})
     * @Gedmo\Versioned
     */
    private $label;

    /**
     * User mail.
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     *
     * @ORM\Column(type="string", unique=true, length=255, nullable=false, name="usr_mail")
     * @Gedmo\Versioned
     */
    private $mail;

    /**
     * User password.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=true, options={"comment":"Mot de passe crypté"})
     * @Gedmo\Versioned
     */
    private $password;

    /**
     * Creation datetime.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="usr_created", options={"comment":"Creation datetime"})
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * Last update datetime.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="usr_updated", options={"comment":"Update datetime"})
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * Roles of this user.
     *
     * @var Role[]
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(
     *     name="tj_userrole",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="usr_id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="rol_id", nullable=false)}
     * )
     */
    private $roles;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * Id getter.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Label/Username getter.
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Mail getter.
     *
     * @return string
     */
    public function getMail(): ?string
    {
        return $this->mail;
    }

    /**
     * Password getter.
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Creation datetime getter.
     *
     * @return DateTime
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Update datetime getter.
     *
     * @return DateTime
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * Roles getter.
     *
     * @return ArrayCollection[Role]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * To implements UserInterface
     *
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Setter of the label of user.
     *
     * @param string $label
     * @return User
     */
    public function setLabel(string $label): User
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Setter of the mail.
     *
     * @param string $mail
     * @return User
     */
    public function setMail(string $mail): User
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * Setter of the password.
     *
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Setter of the roles.
     *
     * @param ArrayCollection $roles
     * @return User
     */
    public function setRoles(ArrayCollection $roles): User
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Return the label of user.
     *
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->getLabel();
    }

    /**
     * Set the username of user.
     *
     * @param  string $username the new username
     * @return User
     */
    public function setUsername(string $username): User
    {
        return $this->setLabel($username);
    }

    /**
     * Erase Credentials.
     *
     * @return User
     */
    public function eraseCredentials(): User
    {
        $this->password = null;

        return $this;
    }

    /**
     * String representation of object.
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     *
     * @see \Serializable::serialize()
     *
     * @return string the string representation of the object or null
     */
    public function serialize(): string
    {
        return serialize(array(
            $this->id,
            $this->label,
            $this->mail,
            $this->password,
            $this->created,
            $this->updated,
        ));
    }

    /**
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized The string representation of the user instance.
     *
     * @return void
     *
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->label,
            $this->mail,
            $this->password,
            $this->created,
            $this->updated,
            ) = unserialize($serialized);
    }

    /**
     * Return if actual user has the mentioned role.
     *
     * @param string $roleCode
     * @return bool  true if the user has the mentioned role
     */
    public function hasRole(string $roleCode): bool
    {
        foreach ($this->roles as $role) {
            if ($role->getCode() == $roleCode) {
                return true;
            }
        }

        return false;
    }
}
