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
namespace App\Tests\Bean;

use App\Bean\Log;
use PHPUnit\Framework\TestCase;

/**
 * Log Bean test case.
 *
 * @category Testing
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class LogTest extends TestCase
{
    /**
     * @var Log
     */
    private $log;

    /**
     * Setup before each unit test.
     */
    protected function setUp()
    {
        $this->log = new Log();
    }

    /**
     * Testing Base::constructor.
     */
    public function testConstructor()
    {
        //No null value for a bean
        self::assertNotNull($this->log->getAction());
        self::assertNotNull($this->log->getUsername());
        self::assertFalse($this->log->isLogged());
        self::assertFalse($this->log->hasVersion());

        //Default value test
        self::assertEmpty($this->log->getVersion());
        self::assertEmpty($this->log->getAction());
        self::assertEmpty($this->log->getUsername());
        self::assertCount(0, $this->log->getData());
    }

    /**
     * Testing getter and setter of Version.
     */
    public function testVersion()
    {
        self::assertFalse($this->log->hasVersion());
        $expected = 42;
        self::assertEquals($this->log, $this->log->setVersion($expected));
        self::assertEquals($expected, $this->log->getVersion());
        self::assertTrue($this->log->hasVersion());

        $expected = 0;
        self::assertEquals($this->log, $this->log->setVersion($expected));
        self::assertEquals($expected, $this->log->getVersion());
        self::assertFalse($this->log->hasVersion());
    }

    /**
     * Testing getter and setter of Action.
     */
    public function testAction()
    {
        $expected = 'action';
        self::assertEquals($this->log, $this->log->setAction($expected));
        self::assertEquals($expected, $this->log->getAction());
    }

    /**
     * Testing getter and setter of Data.
     */
    public function testData()
    {
        $expected = ['foo', 'bar'];
        self::assertEquals($this->log, $this->log->setData($expected));
        self::assertEquals($expected, $this->log->getData());
    }

    /**
     * Testing getter and setter of Username.
     */
    public function testUsername()
    {
        $expected = 'username';
        self::assertEquals($this->log, $this->log->setUsername($expected));
        self::assertEquals($expected, $this->log->getUsername());

        $expected = '';
        self::assertEquals($this->log, $this->log->setUsername(null));
        self::assertEquals($expected, $this->log->getUsername());
    }

    /**
     * Testing getter and setter of Logged.
     */
    public function testLogged()
    {
        self::assertFalse($this->log->isLogged());
        $expected = new \DateTime();
        self::assertEquals($this->log, $this->log->setLogged($expected));
        self::assertEquals($expected, $this->log->getLogged());
        self::asserttrue($this->log->isLogged());
    }
}
