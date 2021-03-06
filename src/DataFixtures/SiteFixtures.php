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
 * Loading site class.
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
            //Default site
            $site = new Site();
            $site->setLabel('Site noir');
            $site->setColor('000000');

            $this->addReference('site_noir', $site);

            $site1 = new Site();
            $site1->setLabel('Site rouge');
            $site1->setColor('FF0000');

            $this->addReference('site_rouge', $site1);

            //Deleted site
            $siteDeleted = new Site();
            $siteDeleted->setLabel('Site banni');
            $siteDeleted->setColor('050000');

            //Persist dev and test data
            $manager->persist($site);
            $manager->persist($site1);
            $manager->persist($siteDeleted);
            $manager->flush();

            //We delete banned site
            $manager->remove($siteDeleted);
        } else {
            $site = new Site();
            $site->setLabel('Site principal');
            $site->setColor('000088');

            $this->addReference('site_default', $site);
            $manager->persist($site);

        }

        $manager->flush();
    }
}
