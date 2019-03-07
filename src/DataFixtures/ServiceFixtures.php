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

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Service;

/**
 * LoadUserData class.
 *
 * @category DataFixtures
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class ServiceFixtures extends Fixture
{
    /**
     * Load Data.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var User $administrator */
        $administrator = $this->getReference('user_admin');
        $services = [
            'DNS',
            'Firewall',
            'NTP',
        ];

        if (in_array($this->container->get('kernel')->getEnvironment(), ['dev', 'test'])) {
            //Load dev and test data
            $services[] = 'Service 0';
        }

        foreach ($services as $label) {
            $service = new Service();
            $service->setLabel($label);
            $service->setCreator($administrator);

            $this->addReference('service_'.strtolower($label), $service);

            //Persist dev and test data
            $manager->persist($service);
        }

        $manager->flush();
    }

    /**
     * This method return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array
     */
    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
