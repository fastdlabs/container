<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/4/24
 * Time: 下午6:00
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Container\Tests;

use FastD\Container\Container;
use FastD\Container\Service;
use FastD\Container\Tests\Services\A;
use FastD\Container\Tests\Services\B;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        $this->container = new Container([
            'test' => A::class
        ]);
    }

    public function testService()
    {
        $service = new Service(A::class);

        $this->assertEquals(A::class, $service->getClass());

        $this->assertEquals(null, $service->getConstructor());
        $this->assertNull($service->getConstructor());

        $this->assertEquals(A::class, $service->getName());
    }

    public function testInstance()
    {
        $service = new Service(A::class);

        $service->setContainer($this->container);

        $instance = $service->instance();

        $this->assertEquals($instance->age, 18);

        $instance->age = 15;

        $this->assertEquals($instance->age, 15);

        $instance2 = $service->instance();

        $this->assertEquals(18, $instance2->age);
    }

    public function testSingleton()
    {
        $service = new Service(A::class);

        $instance = $service->singleton();

        $this->assertEquals(18, $instance->age);

        $instance->age = 15;

        $instance2 = $service->singleton();

        $this->assertEquals(15, $instance2->age);
    }

    public function testParameters()
    {

    }
}