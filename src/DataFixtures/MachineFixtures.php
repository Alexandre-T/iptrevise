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

use App\Entity\Service;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Machine;

/**
 * Load Machine Data for tests.
 *
 * @category DataFixtures
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class MachineFixtures extends Fixture
{
    /**
     * Load Data.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if (in_array($this->container->get('kernel')->getEnvironment(), ['dev', 'test'])) {
            $machine = [];
            /** @var User $administrator */
            $administrator = $this->getReference('user_admin');
            /** @var User $organiser */
            $organiser = $this->getReference('user_organiser');
            /** @var Service $dnsService */
            $dnsService = $this->getReference('service_dns');

            for ($index = 0; $index <= 91; ++$index) {
                $machine[$index] = (new Machine())
                    ->setLabel("Machine $index")
                    ->setDescription("Description $index")
                    ->setInterface($index % 8 + 1)
                    ->setCreator($organiser);

                if ($index % 5) {
                    $machine[$index]
                        ->addService($dnsService)
                        ->setCreator($administrator);
                }

                if ($index == 91) {
                    $machine[91]->setLabel('Machine bannie');
                }

                $this->addReference("machine_$index", $machine[$index]);
                $manager->persist($machine[$index]);
            }
            $manager->flush();
            $manager->remove($machine[91]);
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
