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
        $this->assertInstanceOf(Container::class, $container);
    }

    public function testAddObj()
    {
        $this->container->add('container', new Container());
        $container = $this->container->get('container');
        $this->assertInstanceOf(Container::class, $container);
    }

    public function testAddClosure()
    {
        $this->container->add('closure', function () {return "OK";});
        $closure = $this->container->get('closure');
        $this->assertEquals("OK", $closure());
    }

    public function testHas()
    {
        $this->container->add('container', Container::class);
        $this->assertTrue($this->container->has('container'));
        $this->assertFalse($this->container->has('not_exist'));
    }

    public function testGetNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->container->get('not_exist');
    }

    public function testSingleton()
    {
        $this->container->add('container', Container::class);
        $container1 = $this->container->get('container');
        $container2 = $this->container->get('container');
        $this->assertSame($container1, $container2);
    }

    public function testRemove()
    {
        $this->container->add('container', Container::class);
        $this->assertTrue($this->container->has('container'));
        $this->container->remove('container');
        $this->assertFalse($this->container->has('container'));
    }

    public function testArrayAccess()
    {
        // offsetSet
        $this->container->add('container', Container::class);
        // offsetExists
        $this->assertTrue($this->container->has('container'));
        // offsetGet
        $this->assertInstanceOf(Container::class, $this->container->get('container'));
        // offsetUnset
        $this->container->remove('container');
        $this->assertFalse($this->container->has('container'));
    }

    public function testIterator()
    {
        $this->container->add('container1', Container::class);
        $this->container->add('container2', Container::class);
        $this->container->add('container3', Container::class);

        $keys = [];
        foreach ($this->container as $key => $value) {
            $keys[] = $key;
        }

        $this->assertEquals(['container1', 'container2', 'container3'], $keys);
    }

    public function testRegister()
    {
        $provider = new TestServiceProvider();
        $this->container->register($provider);
        
        $this->assertTrue($this->container->has('test.service'));
    }

    public function testMap()
    {
        // Test class name mapping
        $this->container->add('container', Container::class);
        $this->assertTrue($this->container->has(Container::class));
        $this->assertInstanceOf(Container::class, $this->container->get(Container::class));

        // Test object mapping
        $obj = new stdClass();
        $this->container->add('std', $obj);
        $this->assertTrue($this->container->has(get_class($obj)));
        $this->assertSame($obj, $this->container->get(get_class($obj)));
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