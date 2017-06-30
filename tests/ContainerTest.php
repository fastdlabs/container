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

        $this->container = new FastD\Container\Container();
    }

    /**
     * @expectedException \FastD\Container\NotFoundException
     */
    public function testContainerClosure()
    {
        $this->container->add('timezone', function () {
            return new DateTimeZone('UTC');
        });

        $this->container->add('date', function () {
            return new DateTime('now', $this->container->get('timezone'));
        });

        $date = $this->container->get('date');

        $date2 = $this->container['date'];

        $this->assertEquals($date, $date2);

        $this->assertTrue(isset($this->container['date']));

        $this->container['timestamp'] = function () {
            return new class
            {
            };
        };

        $this->assertTrue(isset($this->container['timestamp']));

        unset($this->container['date']);

        $this->container['date'];
    }

    public function testContainerInjection()
    {
        $this->container->injectOn(
            'date',
            function (DateTimeZone $dateTimeZone) {
                return new DateTime('now', $dateTimeZone);
            }
        )->withArguments([new DateTimeZone('UTC')]);

        $date = $this->container->make('date');

        $this->assertEquals($date, $this->container->get('date'));
    }

    public function testContainerClosureInjection()
    {
        $this->container->add('zone', new DateTimeZone('UTC'));

        $this->container->injectOn('date', function (DateTimeZone $dateTimeZone) {
            return new DateTime('now', $dateTimeZone);
        });

        $date = $this->container->make('date');

        $this->assertEquals($date, $this->container->get('date'));
    }
}