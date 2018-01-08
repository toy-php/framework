<?php

namespace Toy;

use PHPUnit\Framework\TestCase;
use Toy\Exceptions\Exception;
use Toy\Model\Entity;

class EntityTest extends TestCase
{

    /**
     * @var Entity
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new Entity(1);
    }

    public function testGetId()
    {
        $this->assertEquals(1, $this->entity->getId());
    }

    public function testWithId()
    {
        $this->expectException(Exception::class);
        $this->entity->withId();
    }
}
