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
 */

namespace App\DataFixtures;

use App\Entity\Machine;
use App\Entity\Network;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Ip;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Load Ip Data for tests.
 *
 * @category DataFixtures
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 */
class LoadIpData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Set the container to handle some services.
     *
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load Data.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if (in_array($this->container->get('kernel')->getEnvironment(), ['dev', 'test'])) {

            $ip = [];

            for($index = 0; $index <= 45; $index++){
                /** @var Network $network */
                $network = $this->getReference("network_" .  ceil($index / 3));
                $ip[$index] = (new Ip())
                    ->setNetwork($network)
                    ->setLabel("Ip $index")
                    ->setDescription("Description $index")
                    ->setIp($network->getIp() + $index);

                if ($index % 2){
                    /** @var Machine $machine */
                    $machine = $this->getReference("machine_$index");
                    $ip[$index]->setMachine($machine);
                }

                $this->addReference("ip_$index", $ip[$index]);
                $manager->persist($ip[$index]);
            }
        }

        $manager->flush();
    }

    /**
     * Set the order in which fixtures will be loaded.
     * the lower the number, the sooner that this fixture is loaded.
     *
     * @return int
     */
    public function getOrder()
    {
        return 40;
    }
}
