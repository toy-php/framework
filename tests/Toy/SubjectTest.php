<?php

namespace Toy;

use PHPUnit\Framework\TestCase;
use Toy\Base\Subject;

class SubjectTest extends TestCase
{

    /**
     * @var Subject
     */
    protected $subject;

    /**
     * @var \SplObserver
     */
    protected $observer;

    protected function setUp()
    {
        $this->subject = new Subject();
        $this->observer = $this->createMock(\SplObserver::class);
    }

    public function testAttach()
    {
        $this->assertNull($this->subject->attach($this->observer));
    }

    public function testDetach()
    {
        $this->assertNull($this->subject->detach($this->observer));
    }

    public function testNotify()
    {
        $this->assertNull($this->subject->notify());
    }

    public function testNotifyAttached()
    {
        $this->expectException(\Exception::class);
        $this->observer
            ->expects($this->any())
            ->method('update')
            ->willThrowException(new \Exception());
        $this->subject->attach($this->observer);
        $this->subject->notify();
    }

    public function testNotifyDetached()
    {
        $this->observer
            ->expects($this->any())
            ->method('update')
            ->willThrowException(new \Exception());
        $this->subject->attach($this->observer);
        $this->subject->detach($this->observer);
        $this->assertNull($this->subject->notify());
    }
}
