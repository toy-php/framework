<?php

namespace Toy;

use Toy\Interfaces\AggregateInterface;
use Toy\Model\Aggregate;

class AggregateTest extends \PHPUnit\Framework\TestCase
{

    public function test__clone()
    {
        $foo = new Aggregate(1);
        $bar = clone $foo;
        $this->assertEquals($bar->getState(), Aggregate::STATE_DUTY);
    }

    public function testIsNew()
    {
        $foo = new Aggregate(1);
        $this->assertTrue($foo->isNew());
    }

    public function testIsClean()
    {
        $foo = new Aggregate(1);
        $this->assertTrue($foo->withCleanState()->isClean());
    }

    public function testIsDuty()
    {
        $foo = new Aggregate(1);
        $this->assertTrue($foo->withDutyState()->isDuty());
    }

    public function testIsRemoved()
    {
        $foo = new Aggregate(1);
        $this->assertTrue($foo->withRemovedState()->isRemoved());
    }

    public function testWithCleanState()
    {
        $foo = new Aggregate(1);
        $bar = $foo->withCleanState();
        $this->assertInstanceOf(AggregateInterface::class, $bar);
    }

    public function testWithCleanStateImmutable()
    {
        $foo = new Aggregate(1);
        $bar = $foo->withCleanState();
        $this->assertNotEquals($foo, $bar);
    }

    public function testWithDutyState()
    {
        $foo = new Aggregate(1);
        $bar = $foo->withDutyState();
        $this->assertInstanceOf(AggregateInterface::class, $bar);
    }

    public function testWithDutyStateImmutable()
    {
        $foo = new Aggregate(1);
        $bar = $foo->withDutyState();
        $this->assertNotEquals($foo, $bar);
    }

    public function testWithRemovedState()
    {
        $foo = new Aggregate(1);
        $bar = $foo->withRemovedState();
        $this->assertInstanceOf(AggregateInterface::class, $bar);
    }

    public function testWithRemovedStateImmutable()
    {
        $foo = new Aggregate(1);
        $bar = $foo->withRemovedState();
        $this->assertNotEquals($foo, $bar);
    }


}
