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

use FastD\Container\ServiceProvider;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceProvider
     */
    private $provider;

    public function setUp()
    {
        if (!class_exists('FastD\Container\Tests\DemoService')) {
            include __DIR__ . '/DemoService.php';
        }

        if (!class_exists('FastD\Container\Tests\TestService')) {
            include __DIR__ . '/TestService.php';
        }
        if (!class_exists('FastD\Container\Tests\ArgsService')) {
            include __DIR__ . '/ArgsService.php';
        }
        if (!class_exists('FastD\Container\Tests\StaticArgsService')) {
            include __DIR__ . '/StaticArgsService.php';
        }
        if (!class_exists('FastD\Container\Tests\DiService')) {
            include __DIR__ . '/DiService.php';
        }

        $this->provider = new ServiceProvider(array(
            'demo'      => 'FastD\Container\Tests\DemoService',
            'test'      => 'FastD\Container\Tests\TestService::single',
            'args'      => 'FastD\Container\Tests\ArgsService',
            'static'    => 'FastD\Container\Tests\StaticArgsService::single',
            'di'        => 'FastD\Container\Tests\DiService',
            'diS'       => 'FastD\Container\Tests\DiStaticService::single',
        ));
    }

    public function testProviderGetService()
    {
        $demo = $this->provider->getService('demo');

        $this->assertEquals('FastD\Container\Tests\DemoService', get_class($demo));

        $this->assertEquals('demo', $this->provider->getService('demo')->getName());

        $args = $this->provider->getService('args', array('jan'));

        $this->assertEquals('jan', $args->getName());

        $static = $this->provider->getService('static', array('jan'));

        $this->assertEquals('jan', $static->getName());
    }

    public function testDependencyInjection()
    {
        $di = $this->provider->getService('FastD\Container\Tests\DiService', array('name' => 'jan'));

        $this->assertInstanceOf('FastD\Container\Tests\DiStaticService', $di->getDiStatic());

        $this->assertEquals('jan', $di->getName());

        $this->assertInstanceOf(
            'FastD\Container\Tests\DiStaticService',
            $this->provider->callServiceMethod($di, 'demoDiMethod')
        );
    }

    public function testSetInstance()
    {
        $demo = new DemoService();

        $this->provider->setService('demo2', $demo);
    }
}