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
 *
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
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
 * @license CeCILL-B V1
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
     * All value must be null after creation.
     */
    public function testConstructor()
    {
        self::assertNull($this->ip->getCreated());
        self::assertNull($this->ip->getId());
        self::assertNull($this->ip->getMachine());
        self::assertNull($this->ip->getNetwork());
        self::assertNull($this->ip->getReason());
        self::assertNull($this->ip->getUpdated());
        self::assertNull($this->ip->getIp());
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
     * Tests reason getter, setter and aliases.
     */
    public function testReason()
    {
        $expected = $actual = 'reason';

        self::assertEquals($this->ip, $this->ip->setReason($actual));
        self::assertEquals($expected, $this->ip->getReason());
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
