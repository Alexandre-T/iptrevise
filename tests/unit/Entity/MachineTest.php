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
 * @copyright 2017 Cerema
 * @license   CeCILL-B V1
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

namespace App\Tests\Entity;

use App\Entity\Machine;
use App\Entity\Ip;
use App\Entity\Service;
use App\Entity\Tag;
use PHPUnit\Framework\TestCase;

/**
 * Entity machine unit test class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
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
        self::assertNull($this->machine->getLocation());
        self::assertNull($this->machine->getMacs());
        self::assertEquals(1, $this->machine->getInterface());
        self::assertInternalType('int', $this->machine->getInterface());
        self::assertNotNull($this->machine->getIps());
        self::assertNotNull($this->machine->getServices());
        self::assertNotNull($this->machine->getTags());
        self::assertEmpty($this->machine->getIps());
        self::assertEmpty($this->machine->getMacs());
        self::assertEmpty($this->machine->getTags());
        self::assertEmpty($this->machine->getServices());
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
     * Tests location getter, setter and aliases.
     */
    public function testLocation()
    {
        self::assertEquals($this->machine, $this->machine->setLocation('location'));
        self::assertEquals('location', $this->machine->getLocation());
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
     * Tests macs getter, setter and aliases.
     */
    public function testMacs()
    {
        $expected = $actual = "mac1\nmac2";

        self::assertEquals($this->machine, $this->machine->setMacs($actual));
        self::assertEquals($expected, $this->machine->getMacs());
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
     * Tests network getter, setter and aliases.
     */
    public function testServices()
    {
        $service1 = new Service();
        $service2 = new Service();
        $this->machine->addService($service1);
        self::assertCount(1, $this->machine->getServices());
        self::assertTrue($this->machine->getServices()->contains($service1));
        self::assertFalse($this->machine->getServices()->contains($service2));
        $this->machine->addService($service2);
        self::assertCount(2, $this->machine->getServices());
        self::assertTrue($this->machine->getServices()->contains($service1));
        self::assertTrue($this->machine->getServices()->contains($service2));
        $this->machine->removeService($service1);
        self::assertCount(1, $this->machine->getServices());
        self::assertFalse($this->machine->getServices()->contains($service1));
        self::assertTrue($this->machine->getServices()->contains($service2));
        $this->machine->removeService($service2);
        self::assertCount(0, $this->machine->getServices());
        self::assertFalse($this->machine->getServices()->contains($service1));
        self::assertFalse($this->machine->getServices()->contains($service2));
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
