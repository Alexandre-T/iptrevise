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

use App\Entity\Ip;
use App\Entity\Machine;
use App\Entity\Network;
use PHPUnit\Framework\TestCase;

/**
 * Entity ip unit test class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class IpTest extends TestCase
{
    /**
     * @var Ip
     */
    private $ip;

    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        $this->ip = new Ip();
    }

    /**
     * All value must be null after creation
     */
    public function testConstructor()
    {
        self::assertNull($this->ip->getCreated());
        self::assertNull($this->ip->getDescription());
        self::assertNull($this->ip->getId());
        self::assertNull($this->ip->getLabel());
        self::assertNull($this->ip->getMachine());
        self::assertNull($this->ip->getNetwork());
        self::assertNull($this->ip->getUpdated());
        self::assertNull($this->ip->getIp());
    }

    /**
     * Tests label getter, setter and aliases.
     */
    public function testLabel()
    {
        self::assertEquals($this->ip, $this->ip->setLabel('label'));
        self::assertEquals('label', $this->ip->getLabel());
    }

    /**
     * Tests description getter, setter and aliases.
     */
    public function testDescription()
    {
        self::assertEquals($this->ip, $this->ip->setDescription('description'));
        self::assertEquals('description', $this->ip->getDescription());
    }

    /**
     * Tests machine getter, setter and aliases.
     */
    public function testMachine()
    {
        $expected = $actual = new Machine();

        self::assertEquals($this->ip, $this->ip->setMachine($actual));
        self::assertEquals($expected, $this->ip->getMachine());
    }

    /**
     * Tests network getter, setter and aliases.
     */
    public function testNetwork()
    {
        $expected = $actual = new Network();

        self::assertEquals($this->ip, $this->ip->setNetwork($actual));
        self::assertEquals($expected, $this->ip->getNetwork());
    }
    
    /**
     * Tests ip getter, setter and aliases.
     */
    public function testIp()
    {
        $expected = $actual = ip2long('255.255.255.254');

        self::assertEquals($this->ip, $this->ip->setIp($actual));
        self::assertEquals($expected, $this->ip->getIp());
    }
}
