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
use PHPUnit\Framework\TestCase;

/**
 * Entity role unit test class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
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
        self::assertNull($this->role->getId());
        self::assertNull($this->role->getCode());
        self::assertNull($this->role->getCreated());
        self::assertNull($this->role->getLabel());
        self::assertNull($this->role->getUpdated());
        self::assertNotNull($this->role->getUsers());
        self::assertEmpty($this->role->getUsers());
    }

    /**
     * Tests label getter, setter and aliases.
     */
    public function testLabel()
    {
        self::assertEquals($this->role, $this->role->setLabel('label'));
        self::assertEquals('label', $this->role->getLabel());
    }

    /**
     * Tests code getter, setter.
     */
    public function testCode()
    {
        self::assertEquals($this->role, $this->role->setCode('code'));
        self::assertEquals('code', $this->role->getCode());
    }
}
