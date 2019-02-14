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

use App\Entity\Plage;
use PHPUnit\Framework\TestCase;

/**
 * Entity plage unit test class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class PlageTest extends TestCase
{
    /**
     * @var Plage
     */
    private $plage;

    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        $this->plage = new Plage();
    }

    /**
     * All value must be null after creation.
     */
    public function testConstructor()
    {
        self::assertNull($this->plage->getCreated());
        self::assertNull($this->plage->getId());
        self::assertEquals('000000', $this->plage->getColor());
        self::assertNull($this->plage->getLabel());
        self::assertNull($this->plage->getStart());
        self::assertNull($this->plage->getEnd());
        self::assertNull($this->plage->getUpdated());
    }

    /**
     * Tests label getter, setter and aliases.
     */
    public function testLabel()
    {
        $expected = $actual = 'label';

        self::assertEquals($this->plage, $this->plage->setLabel($actual));
        self::assertEquals($expected, $this->plage->getLabel());
    }

    /**
     * Tests start ip getter, setter and aliases.
     */
    public function testStart()
    {
        $expected = $actual = ip2long('255.255.255.250');

        self::assertEquals($this->plage, $this->plage->setStart($actual));
        self::assertEquals($expected, $this->plage->getStart());
    }

    /**
     * Tests end ip getter, setter and aliases.
     */
    public function testEnd()
    {
        $expected = $actual = ip2long('255.255.255.250');

        self::assertEquals($this->plage, $this->plage->setEnd($actual));
        self::assertEquals($expected, $this->plage->getEnd());
    }

    /**
     * Tests color getter, setter and aliases.
     */
    public function testColor()
    {
        $expected = $actual = 'color';

        self::assertEquals($this->plage, $this->plage->setColor($actual));
        self::assertEquals($expected, $this->plage->getColor());
        self::assertEquals(0, $this->plage->getRed());
        self::assertEquals(0, $this->plage->getGreen());
        self::assertEquals(0, $this->plage->getBlue());

        $expected = $actual = 'FEFDFC';

        self::assertEquals($this->plage, $this->plage->setColor($actual));
        self::assertEquals($expected, $this->plage->getColor());
        self::assertEquals(254, $this->plage->getRed());
        self::assertEquals(253, $this->plage->getGreen());
        self::assertEquals(252, $this->plage->getBlue());
    }
}
