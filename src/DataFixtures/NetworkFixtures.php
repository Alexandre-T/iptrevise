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

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Network;

/**
 * LoadNetworkData class.
 *
 * @category DataFixtures
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 */
class NetworkFixtures extends Fixture
{
    /**
     * Load Data.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if (in_array($this->container->get('kernel')->getEnvironment(), ['dev', 'test'])) {

            $network = [];

            for($index = 0; $index <= 30; $index++){
                $network[$index] = (new Network())
                    ->setLabel("Network $index")
                    ->setDescription("Description $index")
                    ->setColor("000000")
                    ->setIp(ip2long("192.168.$index.0"))
                    ->setMask(32);

                $this->addReference("network_$index", $network[$index]);
                $manager->persist($network[$index]);
            }
        }

        $manager->flush();
    }
}
