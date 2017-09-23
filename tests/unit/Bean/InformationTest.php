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
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 */
namespace App\Tests\Bean;

use App\Bean\Information;
use PHPUnit\Framework\TestCase;

/**
 * Information Bean test case.
 *
 * @category Testing
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 */
class InformationTest extends TestCase
{
    /**
     * @var Information
     */
    private $information;

    /**
     * Setup before each unit test.
     */
    protected function setUp()
    {
        $this->information = new Information();
    }

    /**
     * Testing Base::constructor.
     */
    public function testConstructor()
    {
        //No null value for a bean
        self::assertFalse($this->information->isCreated());
        self::assertFalse($this->information->isUpdated());

        self::assertNull($this->information->getCreated());
        self::assertNull($this->information->getUpdated());
    }
    
    /**
     * Testing getter and setter of Updated.
     */
    public function testUpdated()
    {
        self::assertFalse($this->information->isUpdated());
        $expected = new \DateTime();
        self::assertEquals($this->information, $this->information->setUpdated($expected));
        self::assertEquals($expected, $this->information->getUpdated());
        self::asserttrue($this->information->isUpdated());
    }

    /**
     * Testing getter and setter of Created.
     */
    public function testCreated()
    {
        self::assertFalse($this->information->isCreated());
        $expected = new \DateTime();
        self::assertEquals($this->information, $this->information->setCreated($expected));
        self::assertEquals($expected, $this->information->getCreated());
        self::asserttrue($this->information->isCreated());
    }
}
