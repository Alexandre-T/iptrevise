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
        $roleAdmin= $this->getReference('role_admin');

        $userAlexandre = new User();
        $userAlexandre->setUsername('Alexandre');
        $userAlexandre->setMail('alexandre.tranchant@cerema.fr');
        $userAlexandre->setPassword('$2y$10$eKktQf5LJLOnM7tZvjyIkeJu34wPeU9LWZ8HMXe/m8y6K8.kRLQCK');
        $userAlexandre->getRoles()->add($roleAdmin);

        $this->addReference('user_alexandre', $userAlexandre);

        if (in_array($this->container->get('kernel')->getEnvironment(), ['dev', 'test'])) {
            //Load dev and test data
            // I add one user for each role (to test the security component)

            //Retrieve roles
            $roleReader = $this->getReference('role_reader');
            $roleOrganizer = $this->getReference('role_organizer');
            $roleUser = $this->getReference('role_user');

            //Encrypt password
            $encoder = $this->container->get('security.password_encoder');

            //Reader
            $userReader = new User();
            $userReader->setUsername('Reader');
            $userReader->setMail('reader@example.org');
            $userReader->setPassword($encoder->encodePassword($userReader, 'reader'));
            $userReader->getRoles()->add($roleReader);

            //Organizer
            $userOrganizer = new User();
            $userOrganizer->setUsername('Organizer');
            $userOrganizer->setMail('organizer@example.org');
            $userOrganizer->setPassword($encoder->encodePassword($userOrganizer, 'organizer'));
            $userOrganizer->getRoles()->add($roleOrganizer);

            //User
            $userUser = new User();
            $userUser->setUsername('User');
            $userUser->setMail('user@example.org');
            $userUser->setPassword($encoder->encodePassword($userUser, 'user'));
            $userUser->getRoles()->add($roleUser);

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
