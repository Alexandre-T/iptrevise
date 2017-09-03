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

use App\Bean\Data;
use PHPUnit\Framework\TestCase;

/**
 * Data Bean test case.
 *
 * @category Testing
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class DataTest extends TestCase
{
    /**
     * @var Data
     */
    private $data;

    /**
     * Setup before each unit test.
     */
    protected function setUp()
    {
        $this->data = new Data();
    }

    /**
     * Testing Base::constructor.
     */
    public function testConstructor()
    {
        //No null value for a bean
        self::assertNotNull($this->data->getId());
        self::assertNotNull($this->data->getEntity());
        self::assertNotNull($this->data->getLabel());
        self::assertNotNull($this->data->getName());
        
        //Default value test
        self::assertEquals(0, $this->data->getId());
        self::assertEquals('settings.label', $this->data->getLabel());
        self::assertFalse($this->data->isNoMore());
        self::assertFalse($this->data->isNone());
        self::assertFalse($this->data->hasId());
        self::assertFalse($this->data->hasName());
    }

    /**
     * Testing getter and setter of Id.
     */
    public function testId()
    {
        self::assertFalse($this->data->hasId());
        $expected = 42;
        self::assertEquals($this->data, $this->data->setId($expected));
        self::assertEquals($expected, $this->data->getId());
        self::assertTrue($this->data->hasId());

        $expected = 0;
        self::assertEquals($this->data, $this->data->setId($expected));
        self::assertEquals($expected, $this->data->getId());
        self::assertFalse($this->data->hasId());
    }

    /**
     * Testing getter and setter of Entity.
     */
    public function testEntity()
    {
        $expected = 'entity';
        self::assertEquals($this->data, $this->data->setEntity($expected));
        self::assertEquals($expected, $this->data->getEntity());
    }
    
    /**
     * Testing getter and setter of Translate.
     */
    public function testTranslate()
    {
        self::assertEquals($this->data, $this->data->setTranslate(true));
        self::assertTrue($this->data->getTranslate());

        self::assertEquals($this->data, $this->data->setTranslate(false));
        self::assertFalse($this->data->getTranslate());
    }

    /**
     * Testing getter and setter of Label.
     */
    public function testLabel()
    {
        $expected = 'label';
        self::assertEquals($this->data, $this->data->setLabel($expected));
        self::assertEquals($expected, $this->data->getLabel());
    }


    /**
     * Testing getter and setter of Name.
     */
    public function testName()
    {
        self::assertFalse($this->data->hasName());
        $expected = 'name';
        self::assertEquals($this->data, $this->data->setName($expected));
        self::assertEquals($expected, $this->data->getName());
        self::asserttrue($this->data->hasName());
    }

    /**
     * Testing getter and setter of NoMore.
     */
    public function testNoMore()
    {
        $expected = true;
        self::assertEquals($this->data, $this->data->setNoMore($expected));
        self::assertEquals($expected, $this->data->isNoMore());
    }

    /**
     * Testing getter and setter of None.
     */
    public function testNone()
    {
        $expected = true;
        self::assertEquals($this->data, $this->data->setNone($expected));
        self::assertEquals($expected, $this->data->isNone());
    }
}
