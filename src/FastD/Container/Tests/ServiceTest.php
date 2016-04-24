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
use FastD\Container\Tests\Libs\TestService;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $container;

    public function setUp()
    {
        $this->container = new Container([
            'test' => TestService::class
        ]);
    }

    public function testService()
    {
        $service = new Service(TestService::class);

        $this->assertEquals(TestService::class, $service->getClass());

//        $this->assertEquals(null, $service->getConstructor());
        $this->assertNull($service->getConstructor());

        $this->assertEquals(TestService::class, $service->getName());
    }

    public function testContainer()
    {
        $service = new Service(TestService::class);

        $service->setContainer($this->container);
    }
}