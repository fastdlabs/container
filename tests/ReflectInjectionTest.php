<?php
use FastD\Container\ReflectInjection;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class ReflectInjectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        include_once __DIR__ . '/Services/ConstructorInjection.php';
        include_once __DIR__ . '/Services/MethodInjection.php';
        include_once __DIR__ . '/Services/StaticInjection.php';
        include_once __DIR__ . '/Services/Reflect.php';
    }

    public function testReflectInjection()
    {
        $injection = new ReflectInjection();

        $injection->injectOn(Reflect::class);
    }
}
