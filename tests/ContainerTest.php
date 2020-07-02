<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/3/11 仅以此时，怀念过去的自己
 * Time: 下午2:38
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

use FastD\Container\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        $this->container = new FastD\Container\Container();
    }

    public function testContainerClassString()
    {
        $this->container->add('timezone', DateTimeZone::class);
        list(, $callback, ) = $this->container->get('timezone');
        $this->assertEquals(DateTimeZone::class, $callback);
    }

}
