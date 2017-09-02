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
 *
 */
namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * LoadUserData class.
 *
 * @category DataFixtures
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class LoadUserData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Set the container to handle some services.
     *
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load Data.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var Role $roleAdmin */
        $roleAdmin= $this->getReference('role_admin');

        $userAlexandre = new User();
        $userAlexandre->setUsername('Alexandre');
        $userAlexandre->setMail('alexandre.tranchant@cerema.fr');
        $userAlexandre->setPassword('$2y$10$eKktQf5LJLOnM7tZvjyIkeJu34wPeU9LWZ8HMXe/m8y6K8.kRLQCK');
        $userAlexandre->addRole($roleAdmin);

        $this->addReference('user_alexandre', $userAlexandre);

        if (in_array($this->container->get('kernel')->getEnvironment(), ['dev', 'test'])) {
            //Load dev and test data
            // I add one user for each role (to test the security component)

            //Retrieve roles
            /** @var Role $roleReader */
            $roleReader = $this->getReference('role_reader');
            /** @var Role $roleOrganizer */
            $roleOrganizer = $this->getReference('role_organizer');
            /** @var Role $roleUser */
            $roleUser = $this->getReference('role_user');

            //Reader
            $userReader = new User();
            $userReader->setUsername('Reader');
            $userReader->setMail('reader@example.org');
            $userReader->setPlainPassword('reader');
            $userReader->addRole($roleReader);

            //Organizer
            $userOrganizer = new User();
            $userOrganizer->setUsername('Organizer');
            $userOrganizer->setMail('organizer@example.org');
            $userOrganizer->setPlainPassword('organizer');
            $userOrganizer->addRole($roleOrganizer);

            //User
            $userUser = new User();
            $userUser->setUsername('User');
            $userUser->setMail('user@example.org');
            $userUser->setPlainPassword('user');
            $userUser->addRole($roleUser);

            //These references are perhaps unuseful.
            $this->addReference('user_reader', $userReader);
            $this->addReference('user_user', $userUser);
            $this->addReference('user_organizer', $userOrganizer);

            //Persist dev and test data
            $manager->persist($userReader);
            $manager->persist($userUser);
            $manager->persist($userOrganizer);
        }
            

        $manager->persist($userAlexandre);
        $manager->flush();
    }

    /**
     * Set the order in which fixtures will be loaded.
     * the lower the number, the sooner that this fixture is loaded
     *
     * @return int
     */
    public function getOrder()
    {
        return 20;
    }
}
