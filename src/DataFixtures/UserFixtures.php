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

use App\Entity\Role;
use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

/**
 * LoadUserData class.
 *
 * @category DataFixtures
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class UserFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Load Data.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $roleAdmin = ['ROLE_ADMIN'];

        if (in_array(getenv('APP_ENV'), [ 'dev', 'test' ])) {
            //Load dev and test data
            // I add one user for each role (to test the security component)

            //Retrieve roles
            $roleReader = ['ROLE_READER'];
            $roleOrganiser = ['ROLE_ORGANISER'];
            $roleUser = ['ROLE_USER'];

            /** @var Site $site1 */
            $site1 = $this->getReference('site_default');
            /** @var Site $site2 */
            $site2 = $this->getReference('site_rouge');

            //Reader
            $userReader = new User();
            $userReader->setLabel('Reader');
            $userReader->setMail('reader@example.org');
            $userReader->setPlainPassword('reader');
            $userReader->setRoles($roleReader);

            //ORGANISERS
            $userOrganiser = new User();
            $userOrganiser->setLabel('Organiser');
            $userOrganiser->setMail('organiser@example.org');
            $userOrganiser->setPlainPassword('organiser');
            $userOrganiser->setRoles($roleOrganiser);

            $userOrganiserSite1 = new User();
            $userOrganiserSite1->setLabel('Organiser1');
            $userOrganiserSite1->setMail('organiser1@example.org');
            $userOrganiserSite1->setPlainPassword('organiser');
            $userOrganiserSite1->setRoles($roleOrganiser);

            $userOrganiserSite2 = new User();
            $userOrganiserSite2->setLabel('Organiser2');
            $userOrganiserSite2->setMail('organiser2@example.org');
            $userOrganiserSite2->setPlainPassword('organiser');
            $userOrganiserSite2->setRoles($roleOrganiser);

            //User
            $userUser = new User();
            $userUser->setLabel('User');
            $userUser->setMail('user@example.org');
            $userUser->setPlainPassword('user');
            $userUser->setRoles($roleUser);

            //Admin
            $userAdministrator = new User();
            $userAdministrator->setLabel('Administrator');
            $userAdministrator->setMail('administrator@example.org');
            $userAdministrator->setPlainPassword('administrator');
            $userAdministrator->setRoles($roleAdmin);

            //These references are perhaps unuseful.
            $this->addReference('user_reader', $userReader);
            $this->addReference('user_user', $userUser);
            $this->addReference('user_organiser', $userOrganiser);
            $this->addReference('user_admin', $userAdministrator);

            //Role affectation
            $roleOrganiserSite1 = new Role();
            $roleOrganiserSite1->setReadOnly(false);
            $roleOrganiserSite1->setSite($site1);
            $roleOrganiserSite1->setUser($userOrganiserSite1);

            //Role affectation
            $roleOrganiserSite2 = new Role();
            $roleOrganiserSite2->setReadOnly(false);
            $roleOrganiserSite2->setSite($site2);
            $roleOrganiserSite2->setUser($userOrganiserSite2);

            //Role affectation
            $roleOrganiserSites = new Role();
            $roleOrganiserSites->setReadOnly(false);
            $roleOrganiserSites->setSite($site1);
            $roleOrganiserSites->setUser($userOrganiser);

            //Role affectation
            $roleOrganiserSites = new Role();
            $roleOrganiserSites->setReadOnly(false);
            $roleOrganiserSites->setSite($site2);
            $roleOrganiserSites->setUser($userOrganiser);

            //Persist dev and test data
            $manager->persist($userReader);
            $manager->persist($userUser);
            $manager->persist($userOrganiser);
            $manager->persist($userAdministrator);

            //Role saving
            $manager->persist($roleOrganiserSite1);
            $manager->persist($roleOrganiserSite2);
            $manager->persist($roleOrganiserSites);
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
            SiteFixtures::class,
        );
    }
}
