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

namespace FastD\Container\Tests;

use FastD\Container\Container;
use FastD\Container\Tests\Libs\TestService;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        $this->container = new Container();

        $this->container->set('demo', 'FastD\\Container\\Tests\\Libs\\TestService::single');
        $this->container->set('demo2', 'FastD\\Container\\Tests\\Libs\\TestService2');
        $this->container->set('demo3', 'FastD\\Container\\Tests\\Libs\\TestConstructor');
        $this->container->set('demo4', 'FastD\\Container\\Tests\\Libs\\TestConstructorArgs');
    }


    public function testConstructArgs()
    {
        $demo = $this->container->get('demo4')->getInstance(['jan']);
        $this->assertEquals('jan', $demo->name);
    }

    public function testConstruct()
    {
        $demo = $this->container->get('demo3');
        $this->assertInstanceOf('FastD\Container\Provider\Service', $demo);
        $this->assertInstanceOf('FastD\Container\Tests\Libs\TestConstructor', $demo->singleton());
        $this->assertInstanceOf('FastD\Container\Tests\Libs\TestConstructor', $demo->getInstance());
    }


    public function testGetService()
    {
        $demo = $this->container->get('demo');
        $this->assertEquals('FastD\Container\Tests\Libs\TestService', $demo->getName());
        $this->assertEquals('single', $demo->getConstructor());
        $this->assertEquals('FastD\Container\Tests\Libs\TestService', $demo->getClass());
    }

    public function testIoC()
    {
        $demo = $this->container->get('demo')->getInstance();
        $this->assertInstanceOf('FastD\Container\Tests\Libs\TestService2', TestService::$ts2);
        $this->assertInstanceOf('FastD\Container\Tests\Libs\TestService', $demo);
        $this->assertTrue($this->container->get('demo')->testIoC());
    }

    public function testSingleton()
    {
        $demo = $this->container->get('demo')->getInstance();
        $demo->setName('janhuang');
        $this->assertEquals('janhuang', $demo->getName());

        $demo2 = $this->container->get('demo')->getInstance();
        $this->assertEquals(null, $demo2->getName());

        $demo3 = $this->container->get('demo')->singleton();
        $this->assertEquals(null, $demo3->getName());
        $demo3->setName('demo3');
        $this->assertEquals('demo3', $demo3->getName());

        $demo4 = $this->container->get('demo')->singleton();
        $this->assertEquals('demo3', $demo4->getName());
    }
}