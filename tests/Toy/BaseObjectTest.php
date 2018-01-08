<?php

namespace Toy;

use PHPUnit\Framework\TestCase;
use Toy\Base\BaseObject;
use Toy\Exceptions\Exception;

class BaseObjectTest extends TestCase
{

    /**
     * @var BaseObject
     */
    protected $object;

    public function setUp()
    {
        $this->object = new BaseObject();
    }

    public function testCreateWithData()
    {
        $object = BaseObject::createWithData([], []);
        $this->assertInstanceOf(BaseObject::class, $object);

    }

    public function testExtractToArray()
    {
        $array = $this->object->extractToArray([]);
        $this->assertTrue(is_array($array));
    }

    public function testGetState()
    {
        $this->assertEquals(0, $this->object->getState());
    }

    public function testWithState()
    {
        $object = $this->object->withState(1);
        $this->assertEquals(1, $object->getState());
    }

    public function test__set()
    {
        $this->expectException(Exception::class);
        $this->object->test = 1;
    }

    public function test__get()
    {
        $this->expectException(Exception::class);
        $this->object->test;
    }

    public function test__isset()
    {
        $this->assertFalse(isset($this->object->test));
    }

    public function test__unset()
    {
        $this->expectException(Exception::class);
        unset($this->object->test);
    }

    public function test__call()
    {
        $this->expectException(Exception::class);
        $this->object->test();
    }

    public function testGet()
    {
        $this->expectException(Exception::class);
        $this->object->getTest();
    }

    public function testHas()
    {
        $this->assertFalse($this->object->hasTest());
    }

    public function testWith()
    {
        $this->expectException(Exception::class);
        $this->object->withTest();
    }
}
