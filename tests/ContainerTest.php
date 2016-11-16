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
        include_once __DIR__ . '/Services/A.php';
        include_once __DIR__ . '/Services/B.php';
        include_once __DIR__ . '/Services/C.php';
        include_once __DIR__ . '/Services/D.php';
    }

    public function testSetGet()
    {
        $container = new Container();

        $container->add('a', new A());

        print_r($container);
    }
}