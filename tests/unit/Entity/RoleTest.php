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
use App\Entity\Site;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Entity role unit test class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 */
class RoleTest extends TestCase
{
    /**
     * @var Role
     */
    private $role;

    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        $this->role = new Role();
    }

    /**
     * All value must be null after creation
     */
    public function testConstructor()
    {
        self::assertNull($this->role->getCreated());
        self::assertTrue($this->role->isReadOnly());
        self::assertNull($this->role->getSite());
        self::assertNull($this->role->getUser());
    }

    /**
     * Tests readOnly getter, setter and aliases.
     */
    public function testReadOnly()
    {
        self::assertEquals($this->role, $this->role->setReadOnly(false));
        self::assertFalse($this->role->isReadOnly());
        self::assertEquals($this->role, $this->role->setReadOnly(true));
        self::assertTrue($this->role->isReadOnly());
    }

    /**
     * Tests site getter, setter and aliases.
     */
    public function testSite()
    {
        $expected = $actual = new Site();
        
        self::assertEquals($this->role, $this->role->setSite($actual));
        self::assertEquals($expected, $this->role->getSite());
    }

    /**
     * Tests user getter, setter and aliases.
     */
    public function testUser()
    {
        $expected = $actual = new User();
        
        self::assertEquals($this->role, $this->role->setUser($actual));
        self::assertEquals($expected, $this->role->getUser());
    }
}
