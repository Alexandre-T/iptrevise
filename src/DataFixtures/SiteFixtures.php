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

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Site;

/**
 * LoadUserData class.
 *
 * @category DataFixtures
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class SiteFixtures extends Fixture
{
    /**
     * Load Data.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        if (in_array($this->container->get('kernel')->getEnvironment(), ['dev', 'test'])) {
            //Load dev and test data
            // I add one user for each role (to test the security component)


            //Site par defait
            $site = new Site();
            $site->setLabel('Site1');
            $site->setColor('black');

            //These references are perhaps unuseful.
            $this->addReference('site_default', $site);

            //Persist dev and test data
            $manager->persist($site);
        }

        $manager->flush();
    }
}
