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

use FastD\Container\Container;
use FastD\Container\Service;

class ServiceTest extends PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        include_once __DIR__ . '/Services/A.php';
        include_once __DIR__ . '/Services/B.php';
        include_once __DIR__ . '/Services/C.php';
        include_once __DIR__ . '/Services/D.php';

        $this->container = new Container([
            'a' => A::class,
            'b' => B::class
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

        $service = new Service(B::class);

        $service->setContainer($this->container);

        $instance = $service->instance([10]);

        $this->assertEquals($instance->a, new A());

        $this->assertEquals(10, $instance->age);
    }

    public function testSingleton()
    {
        $service = new Service(A::class);

        $instance = $service->singleton();

        $this->assertEquals(18, $instance->age);

        $instance->age = 15;

        $instance2 = $service->singleton();

        $this->assertEquals(15, $instance2->age);

        $service = new Service(C::class . '::instance');

        $service->instance();

        $this->assertEquals(1, C::$inc);

        $service = new Service(D::class . '::instance');

        $service->instance([10]);

        $this->assertEquals(10, D::$inc);
    }

    public function testProperty()
    {
        $service = new Service(B::class);

        $this->assertEquals('__construct', $service->getConstructor());

        $service = new Service(A::class);

        $this->assertNull($service->getConstructor());
    }

    public function testParameters()
    {
        $service = new Service(B::class);

        $service->setContainer($this->container);

        $parameters = $service->getParameters($service->getConstructor());

        $this->assertEquals([
            new A()
        ], $parameters);

        $service = new Service(A::class);

        $this->assertEquals([], $service->getParameters($service->getConstructor()));

        $service = new Service(B::class);

        $service->setContainer($this->container);

        $parameters = $service->getParameters($service->getConstructor(), [10]);

        $this->assertEquals([
            new A(),
            10
        ], $parameters);
    }
}