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
    }

    public function testSetGet()
    {
        $this->container->set('test', TestService::class);

        $service = $this->container->get('test');

        $this->assertEquals(TestService::class, $service->getClass());

        $this->assertEquals(null, $service->getConstructor());

        $this->assertEquals(TestService::class, $service->getName());

//        $this->assertEquals(TestService::class, $this->container->get('test'));

//        print_r($this->container->instance('test'));
    }
}