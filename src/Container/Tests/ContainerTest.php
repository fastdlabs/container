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

namespace FastD\Container\Tests;

use FastD\Container\Container;
use FastD\Container\Tests\Services\A;
use FastD\Container\Tests\Services\B;
use FastD\Container\Tests\Services\C;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        $this->container = new Container([
            'a' => A::class,
            'b' => B::class,
        ]);
    }

    public function testSetGet()
    {
        $this->assertTrue($this->container->has('a'));
        $this->assertTrue($this->container->has(A::class));

        $this->assertEquals([
            A::class => A::class,
            B::class => B::class,
        ], $this->container->all());

        $this->assertEquals('a', $this->container->get(A::class)->getName());
        $this->assertEquals('a', $this->container->get('a')->getName());
        $this->assertEquals(A::class, $this->container->get('a')->getClass());
        $this->assertEquals(A::class, $this->container->get(A::class)->getClass());
        $this->assertEquals('a', $this->container->get('a')->getName());
        $this->assertEquals('a', $this->container->get(A::class)->getName());

        $this->assertEquals([
            A::class => $this->container->get(A::class),
            B::class => B::class,
        ], $this->container->all());

        $this->container->set('aa', new A());

        $this->assertEquals(18, $this->container->get('aa')->singleton()->age);
    }

    public function testResetAndInit()
    {
        $this->assertEquals([
            A::class => A::class,
            B::class => B::class,
        ], $this->container->all());

        $this->container->set('c', C::class);

        $this->assertEquals([
            A::class => A::class,
            B::class => B::class,
            C::class => C::class
        ], $this->container->all());

        $this->container->reset();

        $this->assertEquals([
            A::class => A::class,
            B::class => B::class,
        ], $this->container->all());
    }
}