<?php
use FastD\Container\DependDetection;

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class DependDetectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        include_once __DIR__ . '/Services/ConstructorInjection.php';
        include_once __DIR__ . '/Services/MethodInjection.php';
        include_once __DIR__ . '/Services/StaticInjection.php';
    }

    public function testObjectDetection()
    {
        $args = DependDetection::detectionObjectArgs(ConstructorInjection::class, '__construct');

        $this->assertEquals([
            DateTime::class
        ], $args);

        $args = DependDetection::detectionObjectArgs(MethodInjection::class, 'now');

        $this->assertEquals([
            DateTime::class
        ], $args);
    }

    public function testClosureDetection()
    {
        $closure = function (DateTime $dateTime) {
            return $dateTime;
        };

        $args = DependDetection::detectionClosureArgs($closure);

        $this->assertEquals([
            DateTime::class
        ], $args);
    }
}
