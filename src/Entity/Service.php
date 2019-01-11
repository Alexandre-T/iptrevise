<?php
namespace App\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Service entity.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ServiceRepository")
 * @ORM\Table(name="te_service")
 * @Gedmo\Loggable
 */
class Service
{
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
     * Service label
     *
     * @var string
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
     * Datetime last update (in the application) of the network.
     *
     * @var DateTime
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Machine")
     * @ORM\JoinTable(
     *     name="tj_machineservice",
     *     joinColumns={@ORM\JoinColumn(name="service_id", referencedColumnName="ser_id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="machine_id", referencedColumnName="mac_id", nullable=false)}
     * )
     */
    private $services;
}