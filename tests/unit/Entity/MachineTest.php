<?php
/**
 * This file is part of the MACHINE-Trevise Application.
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

use App\Entity\Machine;
use App\Entity\Ip;
use App\Entity\Tag;
use PHPUnit\Framework\TestCase;

/**
 * Entity machine unit test class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class MachineTest extends TestCase
{
    /**
     * @var Machine
     */
    private $machine;

    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        $this->machine = new Machine();
    }

    /**
     * All value must be null after creation
     */
    public function testConstructor()
    {
        self::assertNull($this->machine->getCreated());
        self::assertNull($this->machine->getDescription());
        self::assertNull($this->machine->getId());
        self::assertNull($this->machine->getLabel());
        self::assertEquals(0, $this->machine->getInterface());
        self::assertInternalType('int', $this->machine->getInterface());
        self::assertNotNull($this->machine->getIps());
        self::assertNotNull($this->machine->getTags());
        self::assertEmpty($this->machine->getIps());
        self::assertEmpty($this->machine->getTags());
        self::assertNull($this->machine->getUpdated());
    }

    /**
     * Tests label getter, setter and aliases.
     */
    public function testLabel()
    {
        self::assertEquals($this->machine, $this->machine->setLabel('label'));
        self::assertEquals('label', $this->machine->getLabel());
    }

    /**
     * Tests description getter, setter and aliases.
     */
    public function testDescription()
    {
        self::assertEquals($this->machine, $this->machine->setDescription('description'));
        self::assertEquals('description', $this->machine->getDescription());
    }

    /**
     * Tests interface getter, setter and aliases.
     */
    public function testInterface()
    {
        $expected = $actual = 10;

        self::assertEquals($this->machine, $this->machine->setInterface($actual));
        self::assertEquals($expected, $this->machine->getInterface());
    }

    /**
     * Tests network getter, setter and aliases.
     */
    public function testTags()
    {
        $tag1 = new Tag();
        $tag2 = new Tag();
        $this->machine->addTag($tag1);
        self::assertCount(1, $this->machine->getTags());
        self::assertTrue($this->machine->getTags()->contains($tag1));
        self::assertFalse($this->machine->getTags()->contains($tag2));
        $this->machine->addTag($tag2);
        self::assertCount(2, $this->machine->getTags());
        self::assertTrue($this->machine->getTags()->contains($tag1));
        self::assertTrue($this->machine->getTags()->contains($tag2));
        $this->machine->removeTag($tag1);
        self::assertCount(1, $this->machine->getTags());
        self::assertFalse($this->machine->getTags()->contains($tag1));
        self::assertTrue($this->machine->getTags()->contains($tag2));
        $this->machine->removeTag($tag2);
        self::assertCount(0, $this->machine->getTags());
        self::assertFalse($this->machine->getTags()->contains($tag1));
        self::assertFalse($this->machine->getTags()->contains($tag2));
    }
    
    /**
     * Tests machine getter, setter and aliases.
     */
    public function testIps()
    {
        $ip1 = new Ip();
        $ip2 = new Ip();
        $this->machine->addIp($ip1);
        self::assertCount(1, $this->machine->getIps());
        self::assertTrue($this->machine->getIps()->contains($ip1));
        self::assertFalse($this->machine->getIps()->contains($ip2));
        $this->machine->addIp($ip2);
        self::assertCount(2, $this->machine->getIps());
        self::assertTrue($this->machine->getIps()->contains($ip1));
        self::assertTrue($this->machine->getIps()->contains($ip2));
        $this->machine->removeIp($ip1);
        self::assertCount(1, $this->machine->getIps());
        self::assertFalse($this->machine->getIps()->contains($ip1));
        self::assertTrue($this->machine->getIps()->contains($ip2));
        $this->machine->removeIp($ip2);
        self::assertCount(0, $this->machine->getIps());
        self::assertFalse($this->machine->getIps()->contains($ip1));
        self::assertFalse($this->machine->getIps()->contains($ip2));
    }
}
