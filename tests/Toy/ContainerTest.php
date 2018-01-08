<?php

namespace Toy;

use PHPUnit\Framework\TestCase;
use Toy\Base\Container;
use Toy\Exceptions\Exception;

class ContainerTest extends TestCase
{

    /**
     * @var Container
     */
    protected $container;

    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testFrozen()
    {
        $this->expectException(Exception::class);
        $this->container['test'] = 'test';
        $this->container['test'] = 'test2';
    }

    public function testWithoutFrozen()
    {
        $container = $this->container->withoutFrozen();
        $container['test'] = 'test';
        $container['test'] = 'test2';
        $this->assertEquals('test2', $container['test']);
    }

    public function testOffsetGet()
    {
        $this->expectException(Exception::class);
        $this->container['test'];
    }

    public function testEmptyKeys()
    {
        $this->expectException(Exception::class);
        $this->container[] = 'test';
    }

    public function testInvalidKeys()
    {
        $this->expectException(Exception::class);
        $this->container['123'] = 'test';
    }

    public function testOffsetGetValue()
    {
        $this->container['test'] = 'test';
        $this->assertEquals('test', $this->container['test']);
    }

    public function testOffsetGetCallable()
    {
        $this->container['test'] = function (){
            return 'test';
        };
        $this->assertEquals('test', $this->container['test']);
    }

    public function testOffsetGetObject()
    {
        $this->container['test'] = new \stdClass();
        $this->assertInstanceOf(\stdClass::class, $this->container['test']);
    }

    public function testOffsetExists()
    {
        $this->assertFalse(isset($this->container['test']));
    }

    public function testOffsetExistsValue()
    {
        $this->container['test'] = 'test';
        $this->assertTrue(isset($this->container['test']));
    }

    public function testOffsetUnset()
    {
        $this->assertNull($this->container->offsetUnset('test'));
    }
}
