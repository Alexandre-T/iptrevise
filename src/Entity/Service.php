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

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Service entity.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ServiceRepository")
 * @ORM\Table(name="te_service")
 * @Gedmo\Loggable
 *
 * @UniqueEntity("label", message="form.service.error.label.unique")
 */
class Service implements InformationInterface, LabelInterface
{
    use ReferentTrait;
    /**
     * Service id.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="ser_id", options={"unsigned":true,"comment":"Identifiant du tag"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Service label.
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
     *     name="ser_lib",
     *     options={"comment":"Libellé du tag"}
     * )
     * @Gedmo\Versioned
     */
    private $label;

    /**
     * Datetime creation (in the application) of the service.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="ser_created", options={"comment":"Creation datetime"})
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * Datetime last update (in the application) of the service.
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="ser_updated", options={"comment":"Update datetime"})
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * List of all machines rendering the actual service.
     *
     * @var Machine[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Machine", mappedBy="services")
     */
    private $machines;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->machines = new ArrayCollection();
    }

    /**
     * Get internal identifier.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get label of service.
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
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
     * Get machine rendering the actual service.
     *
     * @return Machine[]|Collection
     */
    public function getMachines(): Collection
    {
        return $this->machines;
    }

    /**
     * Set label of service.
     *
     * @param string $label
     *
     * @return Service
     */
    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Add machine.
     *
     * @param Machine $machine
     *
     * @return Service
     */
    public function addMachine(Machine $machine): self
    {
        $this->machines[] = $machine;

        return $this;
    }

    /**
     * Remove machine.
     *
     * @param Machine $machine
     *
     * @return Service
     */
    public function removeMachine(Machine $machine): self
    {
        $this->machines->removeElement($machine);

        return $this;
    }
}
