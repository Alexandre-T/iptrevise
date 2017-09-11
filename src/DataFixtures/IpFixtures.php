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
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Ip;

/**
 * Load Ip Data for tests.
 *
 * @category DataFixtures
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 */
class IpFixtures extends Fixture
{
    /**
     * Load Data.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if (in_array($this->container->get('kernel')->getEnvironment(), ['dev', 'test'])) {
            $ip = [];
            /** @var User $organiser */
            $organiser = $this->getReference('user_organiser');

            for($index = 0; $index <= 45; $index++){
                /** @var Network $network */
                $network = $this->getReference("network_" .  ceil($index / 3));
                $ip[$index] = (new Ip())
                    ->setNetwork($network)
                    ->setIp($network->getIp() + $index)
                    ->setCreator($organiser);

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
     * This method return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return array(
            MachineFixtures::class,
            NetworkFixtures::class,
            UserFixtures::class,
        );
    }
}
