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
 * @license Cerema 2017
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
        self::assertNull($this->user->getId());
        self::assertNull($this->user->getCreated());
        self::assertNull($this->user->getLabel());
        self::assertNull($this->user->getMail());
        self::assertNull($this->user->getPassword());
        self::assertNull($this->user->getUpdated());
        self::assertNull($this->user->getUsername());
        self::assertEmpty($this->user->getRoles());
        self::assertNull($this->user->getSalt());
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
        $role1 = new Role();
        $role1->setCode('ROLE_USER');

        $role2 = new Role();
        $role2->setCode('ROLE_ADMIN');

        self::assertFalse($this->user->hasRole('foo'));

        $this->user->addRole($role1);

        self::assertFalse($this->user->hasRole('foo'));
        self::assertFalse($this->user->hasRole('ROLE_ADMIN'));
        self::assertTrue($this->user->hasRole('ROLE_USER'));

        $this->user->addRole($role2);
        self::assertFalse($this->user->hasRole('foo'));
        self::assertTrue($this->user->hasRole('ROLE_ADMIN'));
        self::assertTrue($this->user->hasRole('ROLE_USER'));
    }
}
