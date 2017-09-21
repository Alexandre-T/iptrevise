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

use App\Entity\Tag;
use PHPUnit\Framework\TestCase;

/**
 * Entity tag unit test class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class TagTest extends TestCase
{
    /**
     * @var Tag
     */
    private $tag;

    /**
     * Prepares the environment before running a test.
     */
    public function setUp()
    {
        $this->tag = new Tag();
    }

    /**
     * All value must be null after creation
     */
    public function testConstructor()
    {
        self::assertNull($this->tag->getCreated());
        self::assertNull($this->tag->getId());
        self::assertNull($this->tag->getLabel());
        self::assertNotNull((string)($this->tag));
        self::assertEmpty((string)($this->tag));
        self::assertNull($this->tag->getUpdated());
    }

    /**
     * Tests label getter, setter and aliases.
     */
    public function testLabel()
    {
        self::assertEquals($this->tag, $this->tag->setLabel('label'));
        self::assertEquals('label', $this->tag->getLabel());
        self::assertEquals('label', (string)($this->tag));
    }
}
