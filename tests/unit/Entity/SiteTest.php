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

use App\Entity\Network;
use App\Entity\Site;
use PHPUnit\Framework\TestCase;

/**
 * Entity site unit test class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class SiteTest extends TestCase
{
    /**
     * @var Site
     */
    private $site;

    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        $this->site = new Site();
    }

    /**
     * All value must be null after creation.
     */
    public function testConstructor()
    {
        self::assertNull($this->site->getCreated());
        self::assertNull($this->site->getId());
        self::assertNull($this->site->getLabel());
        self::assertNull($this->site->getUpdated());
        self::assertNotNull($this->site->getNetworks());
        self::assertEmpty($this->site->getNetworks());
    }

    /**
     * Tests label getter, setter and aliases.
     */
    public function testLabel()
    {
        self::assertEquals($this->site, $this->site->setLabel('label'));
        self::assertEquals('label', $this->site->getLabel());
    }

    /**
     * Tests color getter, setter and aliases.
     */
    public function testColor()
    {
        self::assertEquals($this->site, $this->site->setColor('color'));
        self::assertEquals('color', $this->site->getColor());
    }

    /**
     * Tests Site get add removeNetwork ().
     */
    public function testNetworks()
    {
        $network1 = new Network();
        $network2 = new Network();
        $this->site->addNetwork($network1);
        self::assertCount(1, $this->site->getNetworks());
        self::assertTrue($this->site->getNetworks()->contains($network1));
        self::assertFalse($this->site->getNetworks()->contains($network2));
        $this->site->addNetwork($network2);
        self::assertCount(2, $this->site->getNetworks());
        self::assertTrue($this->site->getNetworks()->contains($network1));
        self::assertTrue($this->site->getNetworks()->contains($network2));
        $this->site->removeNetwork($network1);
        self::assertCount(1, $this->site->getNetworks());
        self::assertFalse($this->site->getNetworks()->contains($network1));
        self::assertTrue($this->site->getNetworks()->contains($network2));
        $this->site->removeNetwork($network2);
        self::assertCount(0, $this->site->getNetworks());
        self::assertFalse($this->site->getNetworks()->contains($network1));
        self::assertFalse($this->site->getNetworks()->contains($network2));
    }
}
