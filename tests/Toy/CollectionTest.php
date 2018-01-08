<?php

namespace Toy;

use PHPUnit\Framework\TestCase;
use Toy\Exceptions\Exception;
use Toy\Interfaces\CollectionInterface;
use Toy\Interfaces\MetaDataInterface;
use Toy\Model\Collection;

class CollectionTest extends TestCase
{

    /**
     * @var Collection
     */
    protected $collection;

    protected function setUp()
    {
        $this->collection = new Collection(\stdClass::class);
    }

    public function testGetMetaData()
    {
        $metadata = $this->createMock(MetaDataInterface::class);
        $collection = $this->collection->withMetaData($metadata);
        $this->assertInstanceOf(MetaDataInterface::class, $collection->getMetaData());
    }

    public function testWithMetaData()
    {
        $metadata = $this->createMock(MetaDataInterface::class);
        $collection = $this->collection->withMetaData($metadata);
        $this->assertFalse($collection === $this->collection);
    }

    public function testGetType()
    {
        $this->assertEquals(\stdClass::class, $this->collection->getType());
    }

    public function testWithObjects()
    {
        $objects = [
            new \stdClass(),
            new \stdClass(),
            new \stdClass()
        ];
        $collection = Collection::withObjects(\stdClass::class, $objects);
        $this->assertInstanceOf(CollectionInterface::class, $collection);
    }

    public function testWithObject()
    {
        $collection = $this->collection->withObject(new \stdClass());
        $this->assertFalse($collection === $this->collection);
    }

    public function testWithoutObject()
    {
        $object = new \stdClass();
        $collection = $this->collection->withObject($object);
        $collection2 = $collection->withoutObject($object);
        $this->assertFalse($collection === $collection2);
    }

    public function testOffsetGet()
    {
        $object = new \stdClass();
        $collection = $this->collection->withObject($object);
        $this->assertInstanceOf(\stdClass::class, $collection[0]);
    }

    public function testOffsetSet()
    {
        $this->expectException(Exception::class);
        $object = new \stdClass();
        $collection = $this->collection->withObject($object);
        $collection[0] = new \stdClass();
    }

    public function testOffsetSetInvalid()
    {
        $this->expectException(Exception::class);
        $object = $this->getMockBuilder('Test')->getMock();
        $this->collection->withObject($object);
    }

    public function testOffsetExists()
    {
        $object = new \stdClass();
        $collection = $this->collection->withObject($object);
        $this->assertTrue(isset($collection[0]));
    }

    public function testOffsetUnset()
    {
        $this->expectException(Exception::class);
        $object = new \stdClass();
        $collection = $this->collection->withObject($object);
        unset($collection[0]);
    }

    public function testClear()
    {
        $object = new \stdClass();
        $collection = $this->collection->withObject($object);
        $collection->clear();
        $this->assertFalse(isset($collection[0]));
    }

    public function testFilter()
    {
        $object = new \stdClass();
        $object->id = 1;
        $collection = $this->collection->withObject($object);
        $collection = $collection->filter(function ($object) {
            return $object->id == 1;
        });
        $this->assertEquals(1, $collection[0]->id);
    }

    public function testMap()
    {
        $object = new \stdClass();
        $object->id = 1;
        $collection = $this->collection->withObject($object);
        $collection = $collection->map(function ($object) {
            $object->id = 2;
            return $object;
        });
        $this->assertEquals(2, $collection[0]->id);
    }

    public function testReduce()
    {
        $object = new \stdClass();
        $object->id = 2;
        $collection = $this->collection->withObject($object);

        $object2 = new \stdClass();
        $object2->id = 2;
        $collection = $collection->withObject($object2);

        $object3 = new \stdClass();
        $object3->id = 0;

        $object4 = $collection->reduce(function ($result, $object) {
            $result->id += $object->id;
            return $result;
        },
            $object3);

        $this->assertEquals(4, $object4->id);
    }

    public function testSort()
    {
        $object = new \stdClass();
        $object->id = 1;
        $collection = $this->collection->withObject($object);

        $object2 = new \stdClass();
        $object2->id = 2;
        $collection = $collection->withObject($object2);

        $collection->sort(function ($a, $b){
            return -($a->id <=> $b->id);
        });

        foreach ($collection as $item) {
            $this->assertEquals(2, $item->id);
            break;
        }
    }

    public function testSortByField()
    {
        $object = new \stdClass();
        $object->id = 1;
        $collection = $this->collection->withObject($object);

        $object2 = new \stdClass();
        $object2->id = 2;
        $collection = $collection->withObject($object2);

        $collection->sortByField('id', 'desc');

        foreach ($collection as $item) {
            $this->assertEquals(2, $item->id);
            break;
        }
    }

    public function testKeySort()
    {
        $object = new \stdClass();
        $object->id = 1;
        $collection = $this->collection->withObject($object);

        $object2 = new \stdClass();
        $object2->id = 2;
        $collection = $collection->withObject($object2);

        $collection->keySort( 'desc');

        foreach ($collection as $item) {
            $this->assertEquals(2, $item->id);
            break;
        }
    }

    public function testSearch()
    {
        $object = new \stdClass();
        $object->id = 1;
        $collection = $this->collection->withObject($object);

        $object2 = new \stdClass();
        $object2->id = 2;
        $collection = $collection->withObject($object2);

        $object3 = $collection->search('id', 2);
        $this->assertEquals(2, $object3->id);
    }

    public function testCount()
    {
        $object = new \stdClass();
        $object->id = 1;
        $collection = $this->collection->withObject($object);

        $object2 = new \stdClass();
        $object2->id = 2;
        $collection = $collection->withObject($object2);
        $this->assertEquals(2, $collection->count());
    }

    public function testToArray()
    {
        $object = new \stdClass();
        $object->id = 1;
        $collection = $this->collection->withObject($object);

        $object2 = new \stdClass();
        $object2->id = 2;
        $collection = $collection->withObject($object2);

        $this->assertTrue(is_array($collection->toArray()));
    }
}
