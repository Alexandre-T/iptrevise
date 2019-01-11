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
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

namespace App\Tests\Entity;

use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Entity user unit test class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 */
class UserTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        $this->user = new User();
    }

    /**
     * All value must be null after creation
     */
    public function testConstructor()
    {
        self::assertFalse($this->user->isAdmin());
        self::assertNull($this->user->getId());
        self::assertNull($this->user->getCreated());
        self::assertNull($this->user->getLabel());
        self::assertNull($this->user->getMail());
        self::assertNull($this->user->getPassword());
        self::assertNull($this->user->getPlainPassword());
        self::assertNotNull($this->user->getRoles());
        self::assertEquals(['ROLE_USER'], $this->user->getRoles());
        self::assertNull($this->user->getUpdated());
        self::assertNull($this->user->getUsername());
        self::assertNull($this->user->getSalt());
        self::assertNotNull($this->user->getNewRoles());
        self::assertEmpty($this->user->getNewRoles());
    }

    /**
     * Tests label getter, setter and aliases.
     */
    public function testLabel()
    {
        self::assertEquals($this->user, $this->user->setLabel('label'));
        self::assertEquals('label', $this->user->getLabel());

    }

    /**
     * Tests isAdmin getter, setter and aliases.
     */
    public function testAdmin()
    {
        self::assertEquals($this->user, $this->user->setAdmin(true));
        self::assertTrue($this->user->isAdmin());
        self::assertEquals($this->user, $this->user->setAdmin(false));
        self::assertFalse($this->user->isAdmin());

    }

    /**
     * Tests mail getter, setter and aliases.
     */
    public function testMail()
    {
        self::assertEquals($this->user, $this->user->setMail('mail'));
        self::assertEquals('mail', $this->user->getMail());
        self::assertEquals('mail', $this->user->getUsername());

        self::assertEquals($this->user, $this->user->setUsername('label2'));
        self::assertEquals('label2', $this->user->getUsername());
        self::assertEquals('label2', $this->user->getMail());
    }

    /**
     * Tests password setter and erasing
     */
    public function testPassword()
    {
        $expected = $actual = 'toto';

        self::assertEquals($this->user, $this->user->setPassword($actual));
        self::assertEquals($expected, $this->user->getPassword());
        self::assertEquals($this->user, $this->user->eraseCredentials());
        self::assertNull($this->user->getPlainPassword());
    }

    /**
     * Tests plain password setter and erasing
     */
    public function testPlainPassword()
    {
        //I have to initialize password with a foo value
        $this->user->setPassword('foo');

        //I test the setter
        $expected = $actual = 'bar';
        self::assertEquals($this->user, $this->user->setPlainPassword($actual));
        self::assertEquals($expected, $this->user->getPlainPassword());

        //When setter of plain password was called, password must have been reinitialized.
        self::assertNull($this->user->getPassword());
    }

    /**
     * Test serialization.
     */
    public function testSerialize()
    {
        $this->user->setLabel('user label');
        $this->user->setPassword('user password');
        $this->user->setMail('user mail');

        $expected = 'a:6:{i:0;N;i:1;s:10:"user label";i:2;s:9:"user mail";i:3;s:13:"user password";i:4;N;i:5;N;}';
        self::assertEquals($expected, $this->user->serialize());
    }

    /**
     * Test unSerialization.
     */
    public function testUnSerialize()
    {
        $actual = 'a:6:{i:0;N;i:1;s:10:"user label";i:2;s:9:"user mail";i:3;s:13:"user password";i:4;N;i:5;N;}';
        $this->user->unserialize($actual);

        self::assertEquals('user label', $this->user->getLabel());
        self::assertEquals('user mail', $this->user->getUsername());
        self::assertEquals('user mail', $this->user->getMail());
        self::assertEquals('user password', $this->user->getPassword());
    }

    /**
     * Test the hasRole function
     */
    public function testHasRole()
    {
        $roleAdmin = ['ROLE_ADMIN'];

        self::assertFalse($this->user->hasRole('foo'));

        //Add ROLE_USER and test.
        self::assertEquals(['ROLE_USER'], $this->user->getRoles());

        //Add ROLE_ADMIN and test.
        self::assertEquals($this->user, $this->user->setRoles($roleAdmin));
        self::assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $this->user->getRoles());
        self::assertTrue($this->user->hasRole('ROLE_ADMIN'));
        self::assertTrue($this->user->hasRole('ROLE_USER'));

        //Remove ALL roles and test.
        self::assertEquals($this->user, $this->user->setRoles([]));
        self::assertEquals(['ROLE_USER'], $this->user->getRoles());
        self::assertFalse($this->user->hasRole('foo'));
        self::assertFalse($this->user->hasRole('ROLE_ADMIN'));
        self::assertTrue($this->user->hasRole('ROLE_USER'));

    }

    /**
     * Tests User get add removeRole ().
     */
    public function testRoles()
    {
        $role1 = new Role();
        $role2 = new Role();
        $this->user->addRole($role1);
        self::assertCount(1, $this->user->getNewRoles());
        self::assertTrue($this->user->getNewRoles()->contains($role1));
        self::assertFalse($this->user->getNewRoles()->contains($role2));
        $this->user->addRole($role2);
        self::assertCount(2, $this->user->getNewRoles());
        self::assertTrue($this->user->getNewRoles()->contains($role1));
        self::assertTrue($this->user->getNewRoles()->contains($role2));
        $this->user->removeRole($role1);
        self::assertCount(1, $this->user->getNewRoles());
        self::assertFalse($this->user->getNewRoles()->contains($role1));
        self::assertTrue($this->user->getNewRoles()->contains($role2));
        $this->user->removeRole($role2);
        self::assertCount(0, $this->user->getNewRoles());
        self::assertFalse($this->user->getNewRoles()->contains($role1));
        self::assertFalse($this->user->getNewRoles()->contains($role2));
    }
}
