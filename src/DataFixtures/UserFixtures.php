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
use App\Entity\User;

/**
 * LoadUserData class.
 *
 * @category DataFixtures
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class UserFixtures extends Fixture
{
    /**
     * Load Data.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $roleAdmin = ['ROLE_ADMIN'];

        $userAlexandre = new User();
        $userAlexandre->setLabel('Alexandre');
        $userAlexandre->setMail('alexandre.tranchant@cerema.fr');
        $userAlexandre->setPassword('$2y$10$eKktQf5LJLOnM7tZvjyIkeJu34wPeU9LWZ8HMXe/m8y6K8.kRLQCK');
        $userAlexandre->setRoles($roleAdmin);

        $this->addReference('user_alexandre', $userAlexandre);

        if (in_array($this->container->get('kernel')->getEnvironment(), ['dev', 'test'])) {
            //Load dev and test data
            // I add one user for each role (to test the security component)

            //Retrieve roles
            $roleReader = ['ROLE_READER'];
            $roleOrganiser = ['ROLE_ORGANISER'];
            $roleUser = ['ROLE_USER'];

            //Reader
            $userReader = new User();
            $userReader->setLabel('Reader');
            $userReader->setMail('reader@example.org');
            $userReader->setPlainPassword('reader');
            $userReader->setRoles($roleReader);

            //ORGANISER
            $userOrganiser = new User();
            $userOrganiser->setLabel('Organiser');
            $userOrganiser->setMail('organiser@example.org');
            $userOrganiser->setPlainPassword('organiser');
            $userOrganiser->setRoles($roleOrganiser);

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

            //Persist dev and test data
            $manager->persist($userReader);
            $manager->persist($userUser);
            $manager->persist($userOrganiser);
            $manager->persist($userAdministrator);
        }

        $manager->persist($userAlexandre);
        $manager->flush();
    }
}
