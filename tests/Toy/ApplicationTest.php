<?php

namespace Toy;

use PHPUnit\Framework\TestCase;
use Toy\Base\Application;
use Toy\Base\Middleware;
use Toy\Interfaces\MiddlewareInterface;

class ApplicationTest extends TestCase
{

    /**
     * @var Application
     */
    protected $application;

    protected function setUp()
    {
        $this->application = new Application();
    }

    public function testRequired()
    {
        $this->application['test'] = function () {
            return 'test';
        };
        list($test) = $this->application->required(['test']);
        $this->assertEquals('test', $test);
    }

    public function testExtend()
    {
        $this->application['test'] = function () {
            return 'foo';
        };
        $this->application->extend('test')->then(function ($foo) {
            return $foo . 'bar';
        });
        $this->assertEquals('foobar', $this->application['test']);
    }

    public function testGetRaw()
    {
        $this->application['test'] = function () {
        };
        $this->assertInstanceOf(Middleware::class, $this->application->getRaw('test'));
    }

    public function testFactory()
    {
        $this->application['factory'] = $this->application->factory(function () {
            return new \stdClass();
        });
        $test1 = $this->application['factory'];
        $test2 = $this->application['factory'];
        $this->assertFalse($test1 === $test2);
    }
}
