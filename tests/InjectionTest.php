<?php

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
        $injection = new \FastD\Container\Injection(ConstructorInjection::class);

        $injection->withConstruct()->withArguments([
            new DateTime(),
        ]);

        $obj = $injection->make();

        $this->assertEquals($obj->now(), (new DateTime())->format(DateTime::W3C));
    }

    public function testMethodInjection()
    {
        $injection = new \FastD\Container\Injection(MethodInjection::class);

        $injection->withMethod('now')->withArguments([
            new DateTime(),
        ]);

        $obj = $injection->make();

        $this->assertEquals($obj->date, (new DateTime())->format(DateTime::W3C));
    }

    public function testStaticInjection()
    {
        $injection = new \FastD\Container\Injection(StaticInjection::class);

        $injection->withStatic('now')->withArguments([
            new DateTime(),
        ]);

        $injection->make();

        $this->assertEquals(StaticInjection::$date, (new DateTime())->format(DateTime::W3C));
    }

    public function testClosureInjection()
    {
        
    }
}
