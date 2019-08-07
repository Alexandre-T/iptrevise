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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Tag class.
 *
 * @category App\Entity
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 * @ORM\Table(name="te_tag")
 * @Gedmo\Loggable
 * @UniqueEntity("label", message="form.tag.error.label.unique")
 */
class Tag implements InformationInterface, LabelInterface
{
    use ReferentTrait;
    /**
     * Identifier of Tag.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="tag_id", options={"unsigned":true,"comment":"Identifiant du tag"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Label of the tag.
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="16")
     *
     * @ORM\Column(
     *     type="string",
     *     unique=true,
     *     length=16,
     *     nullable=false,
     *     name="tag_lib",
     *     options={"comment":"Libellé du tag"}
     * )
     * @Gedmo\Versioned
     */
    private $label;

    /**
     * Datetime creation of the machine.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="mac_created", options={"comment":"Creation datetime"})
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * Last update datetime of the machine.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="mac_updated", options={"comment":"Update datetime"})
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * Get the internal identifier of this Tag.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the label of this Tag.
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Get the datetime creation of this tag.
     *
     * @return DateTime
     */
    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    /**
     * Get the datetime of the last update.

     *
     * @return DateTime
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * @param mixed $label
     *
     * @return Tag
     */
    public function setLabel(?string $label): Tag
    {
        $this->label = $label;

        return $this;
    }

    /**
     * The magic function the tag label.
     *
     * @return string
     */
    public function __toString(): string
    {
        if (is_null($this->label)) {
            return '';
        }

        return $this->label;
    }
}
