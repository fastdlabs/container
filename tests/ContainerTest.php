<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/3/11
 * Time: 下午2:38
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

use FastD\Container\Container;

class ContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        include_once __DIR__ . '/Services/ConstructorInjection.php';
        include_once __DIR__ . '/Services/MethodInjection.php';
        include_once __DIR__ . '/Services/StaticInjection.php';
        include_once __DIR__ . '/Services/TestServiceProvider.php';

        $this->container = new FastD\Container\Container();
    }

    public function testContainerClassString()
    {
        $this->container->add('timezone', DateTimeZone::class);
        $this->assertEquals(DateTimeZone::class, $this->container->get('timezone'));
    }

    public function testContainerClassHashStatus()
    {
        $this->container->add('timezone', DateTimeZone::class);
        $this->assertTrue($this->container->has('timezone'));
        $this->assertTrue($this->container->has(DateTimeZone::class));
    }

    public function testContainerMakeClassString()
    {
        $this->container->add('timezone', DateTimeZone::class);
        $timezone = $this->container->make('timezone', ['PRC']);
        $this->assertEquals($timezone->getName(), 'PRC');
    }

    public function testContainerClosure()
    {
        $this->container->add('timezone', function () {
            return new DateTimeZone('UTC');
        });

        $this->container->add('date', function () {
            return new DateTime('now', $this->container->get('timezone'));
        });

        $this->assertEquals($this->container->get('timezone'), new DateTimeZone('UTC'));
        $this->assertInstanceOf(DateTimeZone::class, $this->container->get('timezone'));
        $this->assertEquals('UTC', $this->container->get('date')->getTimeZone()->getName());
    }

    public function testContainerObject()
    {
        $this->container->add('timezone', new DateTimeZone('PRC'));
        $this->assertInstanceOf(DateTimeZone::class, $this->container->get('timezone'));
    }

    public function testContainerRegister()
    {
        $this->container->register(new TestServiceProvider());
        $this->assertTrue($this->container->has('timezone'));
        $this->assertInstanceOf(DateTimeZone::class, $this->container->get('timezone'));
    }
}