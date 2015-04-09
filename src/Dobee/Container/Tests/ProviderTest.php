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

namespace Dobee\Container\Tests;

use Dobee\Container\ServiceProvider;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServiceProvider
     */
    private $provider;

    public function setUp()
    {
        if (!class_exists('Dobee\Container\Tests\DemoService')) {
            include __DIR__ . '/DemoService.php';
        }

        if (!class_exists('Dobee\Container\Tests\TestService')) {
            include __DIR__ . '/TestService.php';
        }
        if (!class_exists('Dobee\Container\Tests\ArgsService')) {
            include __DIR__ . '/ArgsService.php';
        }
        if (!class_exists('Dobee\Container\Tests\StaticArgsService')) {
            include __DIR__ . '/StaticArgsService.php';
        }
        if (!class_exists('Dobee\Container\Tests\DiService')) {
            include __DIR__ . '/DiService.php';
        }

        $this->provider = new ServiceProvider(array(
            'demo'      => 'Dobee\Container\Tests\DemoService',
            'test'      => 'Dobee\Container\Tests\TestService::single',
            'args'      => 'Dobee\Container\Tests\ArgsService',
            'static'    => 'Dobee\Container\Tests\StaticArgsService::single',
            'di'        => 'Dobee\Container\Tests\DiService',
        ));
    }

    public function testPrintProviders()
    {
        echo PHP_EOL;
        print_r($this->provider);
    }

    public function testProviderGetService()
    {
        $demo = $this->provider->getService('demo');

        $this->assertEquals('Dobee\Container\Tests\DemoService', get_class($demo));

        $this->assertEquals('demo', $this->provider->getService('demo')->getName());

        $args = $this->provider->getService('args', array('jan'));

        $this->assertEquals('jan', $args->getName());

        $static = $this->provider->getService('static', array('jan'));

        $this->assertEquals('jan', $static->getName());
    }

    public function testDependencyInjection()
    {
        $di = $this->provider->getService('Dobee\Container\Tests\DiService', array('name' => 'jan'));

        $this->assertInstanceOf('Dobee\Container\Tests\DiStaticService', $di->getDiStatic());

        $this->assertEquals('jan', $di->getName());

        $this->assertInstanceOf(
            'Dobee\Container\Tests\DiStaticService',
            $this->provider->callServiceMethod($di, 'demoDiMethod')
        );
    }
}