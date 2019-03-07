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

use App\Entity\Site;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Network;

/**
 * LoadNetworkData class.
 *
 * @category DataFixtures
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
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
            /** @var User $organiser */
            $organiser = $this->getReference('user_organiser');
            /** @var Site $site */
            $site = $this->getReference('site_default');
            /** @var Site $site1 */
            $site1 = $this->getReference('site_rouge');

            for ($index = 0; $index <= 32; ++$index) {
                $network[$index] = (new Network())
                    ->setLabel("Network $index")
                    ->setDescription("Description $index")
                    ->setColor('000000')
                    ->setIp(ip2long("192.168.$index.0"))
                    ->setSite($site)
                    ->setCidr($index);

                if ($index % 5) {
                    $network[$index]->setCreator($organiser);
                    $network[$index]->setSite($site1);
                }

                $this->addReference("network_$index", $network[$index]);
                $manager->persist($network[$index]);
            }
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
          SiteFixtures::class,
        );
    }
}
