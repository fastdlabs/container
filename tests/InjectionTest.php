<?php
use FastD\Container\Injection;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class InjectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        include_once __DIR__ . '/Services/ConstructorInjection.php';
        include_once __DIR__ . '/Services/MethodInjection.php';
        include_once __DIR__ . '/Services/StaticInjection.php';
    }

    public function testConstructInjection()
    {
        $injection = new Injection(ConstructorInjection::class);
        $injection->withConstruct()->withArguments([
            new DateTime(),
        ]);
        $obj = $injection->make();
        $this->assertEquals($obj->now(), (new DateTime())->format(DateTime::W3C));
    }

    public function testMethodInjection()
    {
        $injection = new Injection(MethodInjection::class);
        $injection->withMethod('now')->withArguments([
            new DateTime(),
        ]);
        $obj = $injection->make();
        $this->assertEquals($obj->date, (new DateTime())->format(DateTime::W3C));
    }

    public function testStaticInjection()
    {
        $injection = new Injection(StaticInjection::class);
        $injection->withMethod('now', true)->withArguments([
            new DateTime(),
        ]);
        $injection->make();
        $this->assertEquals(StaticInjection::$date, (new DateTime())->format(DateTime::W3C));
    }

    public function testClosureInjection()
    {
        $injection = new Injection(function (DateTimeZone $dateTimeZone) {
            return new DateTime('now', $dateTimeZone);
        });

        $injection2 = clone $injection;

        $injection->withArguments([
            new DateTimeZone('PRC')
        ]);

        $injection2->withArguments([
            new DateTimeZone('UTC'),
        ]);

        $date1 = $injection->make();
        $date2 = $injection2->make();

        $this->assertEquals('UTC', $date2->getTimeZone()->getName());
        $this->assertEquals('PRC', $date1->getTimeZone()->getName());
    }

    public function testInstanceInjection()
    {
        $injection = new Injection(new MethodInjection());

        $injection->withMethod('now')
            ->withArguments([
                new DateTime(),
            ]);

        $obj = $injection->make();
        $this->assertEquals($obj->date, (new DateTime())->format(DateTime::W3C));
    }
}
