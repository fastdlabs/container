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
    }

    public function testSetGet()
    {
        $container = new Container();

        $container
            ->add('method', new MethodInjection())
            ->withMethod('now')
            ->withArguments([
                new DateTime(),
            ]);

        $methodInjection = $container->get('method');

        $this->assertEquals($methodInjection->date, (new DateTime())->format(DateTime::W3C));
    }

    public function testContainerClosure()
    {
        $container = new Container();

        $container->add('now', function () use ($container) {
            return new DateTime('now', $container->get('time'));
        });

        $container->add('time', function () {
            return new DateTimeZone('UTC');
        });

        $dateTime = $container->get('now');

        print_r($dateTime);
    }
}