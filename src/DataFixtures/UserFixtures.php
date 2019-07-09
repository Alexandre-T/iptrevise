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
            // I add one user, one admin to test the security component
            // I add three readers with access to one or two sites
            // I add three organisers with access to one or two sites

            /** @var Site $site1 */
            $site1 = $this->getReference('site_default');
            /** @var Site $site2 */
            $site2 = $this->getReference('site_rouge');

            //Readers
            $userReader = new User();
            $userReader->setLabel('Reader Sites');
            $userReader->setMail('reader@example.org');
            $userReader->setPlainPassword('reader');

            $userReaderSite1 = new User();
            $userReaderSite1->setLabel('Reader Site1');
            $userReaderSite1->setMail('reader1@example.org');
            $userReaderSite1->setPlainPassword('reader');

            $userReaderSite2 = new User();
            $userReaderSite2->setLabel('Reader Site2');
            $userReaderSite2->setMail('reader2@example.org');
            $userReaderSite2->setPlainPassword('reader');

            //ORGANISERS
            $userOrganiser = new User();
            $userOrganiser->setLabel('Organiser Sites');
            $userOrganiser->setMail('organiser@example.org');
            $userOrganiser->setPlainPassword('organiser');

            $userOrganiserSite1 = new User();
            $userOrganiserSite1->setLabel('Organiser Site1');
            $userOrganiserSite1->setMail('organiser1@example.org');
            $userOrganiserSite1->setPlainPassword('organiser');

            $userOrganiserSite2 = new User();
            $userOrganiserSite2->setLabel('Organiser Site2');
            $userOrganiserSite2->setMail('organiser2@example.org');
            $userOrganiserSite2->setPlainPassword('organiser');

            //User
            $userUser = new User();
            $userUser->setLabel('User');
            $userUser->setMail('user@example.org');
            $userUser->setPlainPassword('user');

            //Admin
            $userAdministrator = new User();
            $userAdministrator->setLabel('Administrator');
            $userAdministrator->setMail('administrator@example.org');
            $userAdministrator->setPlainPassword('administrator');
            $userAdministrator->setRoles($roleAdmin);

            //References.
            $this->addReference('user_reader', $userReader);
            $this->addReference('user_user', $userUser);
            $this->addReference('user_organiser', $userOrganiser);
            $this->addReference('user_admin', $userAdministrator);

            //Privilege
            $roleOrganiserSite1 = $this->createPrivilege($userOrganiserSite1, $site1, false);
            $roleOrganiserSite2 = $this->createPrivilege($userOrganiserSite2, $site2, false);
            $roleOrganiserSite3 = $this->createPrivilege($userOrganiser, $site1, false);
            $roleOrganiserSite4 = $this->createPrivilege($userOrganiser, $site2, false);

            $roleReaderSite1 = $this->createPrivilege($userReaderSite1, $site1, true);
            $roleReaderSite2 = $this->createPrivilege($userReaderSite2, $site2, true);
            $roleReaderSite3 = $this->createPrivilege($userReader, $site1, true);
            $roleReaderSite4 = $this->createPrivilege($userReader, $site2, true);

            //Persist dev and test user data
            $manager->persist($userReader);
            $manager->persist($userUser);
            $manager->persist($userOrganiser);
            $manager->persist($userAdministrator);

            //Role saving
            $manager->persist($roleOrganiserSite1);
            $manager->persist($roleOrganiserSite2);
            $manager->persist($roleOrganiserSite3);
            $manager->persist($roleOrganiserSite4);
            $manager->persist($roleReaderSite1);
            $manager->persist($roleReaderSite2);
            $manager->persist($roleReaderSite3);
            $manager->persist($roleReaderSite4);
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

    /**
     * Create a new Role.
     * 
     * @param User $user
     * @param Site $site
     * @param bool $readOnly
     * @return Role
     */
    private function createPrivilege(User $user, Site $site, bool $readOnly)
    {
        $role = new Role();
        $role->setReadOnly($readOnly);
        $role->setSite($site);
        $role->setUser($user);
        
        return $role;
    }
}
