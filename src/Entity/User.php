<<<<<<< HEAD
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
 * @copyright 2017 Cerema
 * @license   CeCILL-B V1
 *
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;
use Serializable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Entity User.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(
 *     name="ts_user",
 *     options={"comment":"Table entité des utilisateur","charset":"utf8mb4","collate":"utf8mb4_unicode_ci"}
 * )
 *
 * @Gedmo\Loggable
 *
 * @UniqueEntity("mail", message="form.user.error.mail.unique")
 * @UniqueEntity("label", message="form.user.error.label.unique")
 */
class User implements InformationInterface, LabelInterface, UserInterface, Serializable
{
    /**
     * Identifiant.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="usr_id", options={"unsigned":true,"comment":"Identifiant de l'utilisateur"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * User label.
     * This is NOT the identifier nether the 'technical' username.
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="32")
     *
     * @ORM\Column(type="string", unique=true, length=32, nullable=false, name="usr_label", options={"unsigned":true})
     * @Gedmo\Versioned
     */
    private $label;

    /**
     * User mail and identifiant.
     *
     * @var string
     * @Assert\Length(max="255")
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     *
     * @ORM\Column(type="string", unique=true, length=255, nullable=false, name="usr_mail")
     * @Gedmo\Versioned
     */
    private $mail;

    /**
     * User encoded password.
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
     * @var array
     *
     * @Assert\Count(
     *     min = 1,
     *     minMessage="form.user.error.roles.empty"
     * )
     *
     * @ORM\Column(type="json_array", nullable=true, options={"comment":"Roles de l'utilisateur"})
     *
     * @Gedmo\Versioned
     */
    private $roles = [];

    /**
     * New Roles of this user.
     *
     * @var Role[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Role", mappedBy="user")
     */
    private $newRoles = [];

    /**
     * Is this user an admin?
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, name="usr_admin", options={"default":false,"comment":"is user an admin"})
     */
    private $admin = false;

    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @var string
     */
    private $plainPassword;

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
     * The encoded password.
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Return the non-persistent plain password.
     *
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
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
     * Return an array of all role codes to be complient with UserInterface
     * This is NOT the Roles getter.
     *
     * @return array
     */
    public function getRoles(): array
    {
        // give everyone ROLE_USER!
        if (!in_array('ROLE_USER', $this->roles)) {
            $this->roles[] = 'ROLE_USER';
        }

        return $this->roles;
    }

    /**
     * To implements UserInterface.
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Setter of the label of user.
     *
     * @param string $label
     *
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
     *
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
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set the non-persistent plain password.
     *
     * @param string $plainPassword
     *
     * @return User
     */
    public function setPlainPassword(string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        // @see https://knpuniversity.com/screencast/symfony-security/user-plain-password
        $this->password = null;

        return $this;
    }

    /**
     * Setter of the roles.
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): User
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
        return $this->getMail();
    }

    /**
     * Set the username of user.
     *
     * @param string $username the new username
     *
     * @return User
     */
    public function setUsername(string $username): User
    {
        return $this->setMail($username);
    }

    /**
     * Erase Credentials.
     *
     * @return User
     */
    public function eraseCredentials(): User
    {
        $this->plainPassword = null;

        return $this;
    }

    /**
     * String representation of object.
     *
     * @see http://php.net/manual/en/serializable.serialize.php
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
     * Constructs the object.
     *
     * @see http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized the string representation of the user instance
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->label,
            $this->mail,
            $this->password,
            $this->created,
            $this->updated) = unserialize($serialized);
    }

    /**
     * Return if actual user has the mentioned role.
     *
     * @param string $role
     *
     * @return bool true if the user has the mentioned role
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Get the creator of this user.
     *
     * @return User|null
     */
    public function getCreator(): ?User
    {
        //FIXME
        return $this;
    }
}
=======
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
use Symfony\Component\Security\Core\User\UserInterface;
use Serializable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Entity User.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(
 *     name="ts_user",
 *     options={"comment":"Table entité des utilisateur","charset":"utf8mb4","collate":"utf8mb4_unicode_ci"}
 * )
 *
 * @Gedmo\Loggable
 *
 * @UniqueEntity("mail", message="form.user.error.mail.unique")
 * @UniqueEntity("label", message="form.user.error.label.unique")
 */
class User implements InformationInterface, LabelInterface, UserInterface, Serializable
{
    /**
     * Identifiant.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="usr_id", options={"unsigned":true,"comment":"Identifiant de l'utilisateur"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * User label.
     * This is NOT the identifier nether the 'technical' username.
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="32")
     *
     * @ORM\Column(type="string", unique=true, length=32, nullable=false, name="usr_label", options={"unsigned":true})
     * @Gedmo\Versioned
     */
    private $label;

    /**
     * User mail and identifiant.
     *
     * @var string
     * @Assert\Length(max="255")
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     *
     * @ORM\Column(type="string", unique=true, length=255, nullable=false, name="usr_mail")
     * @Gedmo\Versioned
     */
    private $mail;

    /**
     * User encoded password.
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
     * @var array
     *
     * @Assert\Count(
     *     min = 1,
     *     minMessage="form.user.error.roles.empty"
     * )
     *
     * @ORM\Column(type="json_array", nullable=true, options={"comment":"Roles de l'utilisateur"})
     *
     * @Gedmo\Versioned
     */
    private $roles = [];

    /**
     * New Roles of this user.
     *
     * @var Role[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Role", mappedBy="user")
     */
    private $newRoles = [];

    /**
     * Is this user an admin?
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, name="usr_admin", options={"default":false,"comment":"is user an admin"})
     */
    private $admin = false;

    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @var string
     */
    private $plainPassword;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->newRoles = new ArrayCollection();
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
     * The encoded password.
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Return the non-persistent plain password.
     *
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
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
     * Return an array of all role codes to be complient with UserInterface
     * This is NOT the Roles getter.
     *
     * @return array
     */
    public function getRoles(): array
    {
        // give everyone ROLE_USER!
        if (!in_array('ROLE_USER', $this->roles)) {
            $this->roles[] = 'ROLE_USER';
        }

        return $this->roles;
    }

    /**
     * To implements UserInterface.
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Setter of the label of user.
     *
     * @param string $label
     *
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
     *
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
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set the non-persistent plain password.
     *
     * @param string $plainPassword
     *
     * @return User
     */
    public function setPlainPassword(string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        // @see https://knpuniversity.com/screencast/symfony-security/user-plain-password
        $this->password = null;

        return $this;
    }

    /**
     * Setter of the roles.
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): User
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
        return $this->getMail();
    }

    /**
     * Set the username of user.
     *
     * @param string $username the new username
     *
     * @return User
     */
    public function setUsername(string $username): User
    {
        return $this->setMail($username);
    }

    /**
     * Erase Credentials.
     *
     * @return User
     */
    public function eraseCredentials(): User
    {
        $this->plainPassword = null;

        return $this;
    }

    /**
     * String representation of object.
     *
     * @see http://php.net/manual/en/serializable.serialize.php
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
     * Constructs the object.
     *
     * @see http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized the string representation of the user instance
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->label,
            $this->mail,
            $this->password,
            $this->created,
            $this->updated) = unserialize($serialized);
    }

    /**
     * Return if actual user has the mentioned role.
     *
     * @param string $role
     *
     * @return bool true if the user has the mentioned role
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Get the creator of this user.
     *
     * @return User|null
     */
    public function getCreator(): ?User
    {
        //FIXME
        return $this;
    }

    /**
     * Is this user an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }

    /**
     * Get the roles of user.
     *
     * @return Role[]|Collection
     */
    public function getNewRoles(): Collection
    {
        return $this->newRoles;
    }

    /**
     * Set this user as an admin.
     * 
     * @param bool $admin
     *
     * @return User
     */
    public function setAdmin(bool $admin): User
    {
        $this->admin = $admin;
        
        return $this;
    }
    
    /**
     * Add role.
     *
     * @param Role $role
     *
     * @return User
     */
    public function addRole(Role $role): User
    {
        $this->newRoles[] = $role;

        return $this;
    }

    /**
     * Remove role.
     *
     * @param Role $role
     *
     * @return User
     */
    public function removeRole(Role $role): User
    {
        $this->newRoles->removeElement($role);

        return $this;
    }
}
>>>>>>> upstream/master
