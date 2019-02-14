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

use App\Entity\Machine;
use App\Entity\Service;
use PHPUnit\Framework\TestCase;

/**
 * Entity service unit test class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class ServiceTest extends TestCase
{
    /**
     * @var Service
     */
    private $service;

    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        $this->service = new Service();
    }

    /**
     * All value must be null after creation.
     */
    public function testConstructor()
    {
        self::assertNull($this->service->getCreated());
        self::assertNull($this->service->getId());
        self::assertNull($this->service->getLabel());
        self::assertNull($this->service->getUpdated());
        self::assertNotNull($this->service->getMachines());
        self::assertEmpty($this->service->getMachines());
    }

    /**
     * Tests label getter, setter and aliases.
     */
    public function testLabel()
    {
        self::assertEquals($this->service, $this->service->setLabel('label'));
        self::assertEquals('label', $this->service->getLabel());
    }

    /**
     * Tests Service get add removeMachine ().
     */
    public function testMachines()
    {
        $machine1 = new Machine();
        $machine2 = new Machine();
        $this->service->addMachine($machine1);
        self::assertCount(1, $this->service->getMachines());
        self::assertTrue($this->service->getMachines()->contains($machine1));
        self::assertFalse($this->service->getMachines()->contains($machine2));
        $this->service->addMachine($machine2);
        self::assertCount(2, $this->service->getMachines());
        self::assertTrue($this->service->getMachines()->contains($machine1));
        self::assertTrue($this->service->getMachines()->contains($machine2));
        $this->service->removeMachine($machine1);
        self::assertCount(1, $this->service->getMachines());
        self::assertFalse($this->service->getMachines()->contains($machine1));
        self::assertTrue($this->service->getMachines()->contains($machine2));
        $this->service->removeMachine($machine2);
        self::assertCount(0, $this->service->getMachines());
        self::assertFalse($this->service->getMachines()->contains($machine1));
        self::assertFalse($this->service->getMachines()->contains($machine2));
    }
}
