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
use FastD\Container\NotFoundException;
use FastD\Container\ServiceProviderInterface;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    protected Container $container;

    protected function setUp(): void
    {
        $this->container = new FastD\Container\Container();
    }

    public function testAddClass()
    {
        $this->container->add('container', Container::class);
        $container = $this->container->get('container');

    }

}

// Test service provider class
class TestServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->add('test.service', 'test');
    }
}