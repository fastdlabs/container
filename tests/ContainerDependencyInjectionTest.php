<?php
use FastD\Container\Container;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class ContainerDependencyInjectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        include_once __DIR__ . '/Services/ConstructorInjection.php';
        include_once __DIR__ . '/Services/MethodInjection.php';
        include_once __DIR__ . '/Services/StaticInjection.php';
    }

    public function testDependencyInjection()
    {
        $container = new Container();

        $container->add('timeZone', function () {
            return new DateTimeZone('PRC');
        });

        $container->add('zone', new DateTimeZone('UTC'));

        $container->add('date', function (DateTimeZone $dateTimeZone) {
            return new DateTime('now', $dateTimeZone);
        });
        $dateTimeZone = $container->get('zone');
        
        $dateTimeZone = $container->get('date');

        $this->assertInstanceOf(DateTime::class, $dateTimeZone);
        $this->assertEquals(new DateTime('now', new DateTimeZone('PRC')), $dateTimeZone);
        $date = new DateTime('now', new DateTimeZone('PRC'));
        $this->assertNotEquals($date->getTimezone()->getName(), $dateTimeZone->getTimezone()->getName());
    }
}
