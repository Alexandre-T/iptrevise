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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Load Roles.
 *
 * @category DataFixtures
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class LoadRoleData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
        $roleAdmin = new Role();
        $roleAdmin->setLabel('Administrateur');
        $roleAdmin->setCode('ROLE_ADMIN');

        $roleOrganizer = new Role();
        $roleOrganizer->setLabel('Organiseur');
        $roleOrganizer->setCode('ROLE_ORGANIZER');

        $roleReader = new Role();
        $roleReader->setLabel('Lecteur');
        $roleReader->setCode('ROLE_READER');

        $roleUser = new Role();
        $roleUser->setLabel('Utilisateur Cerbère');
        $roleUser->setCode('ROLE_USER');

        $this->addReference('role_admin', $roleAdmin);
        $this->addReference('role_organizer', $roleOrganizer);
        $this->addReference('role_reader', $roleReader);
        $this->addReference('role_user', $roleUser);

        $manager->persist($roleAdmin);
        $manager->persist($roleOrganizer);
        $manager->persist($roleReader);
        $manager->persist($roleUser);
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
        return 10;
    }
}
