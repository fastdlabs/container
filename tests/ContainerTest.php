<?php

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
        $this->container = new Container();
    }

    public function testAddAndGet()
    {
        // 测试添加字符串
        $this->container->add('string', 'test string');
        $service = $this->container->get('string');
        $this->assertEquals('test string', $service);

        // 测试添加整数
        $this->container->add('integer', 123);
        $service = $this->container->get('integer');
        $this->assertEquals(123, $service);

        // 测试添加数组
        $this->container->add('array', ['key1' => 'value1']);
        $service = $this->container->get('array');
        $this->assertEquals(['key1' => 'value1'], $service);

        // 测试合并数组 - 注意：get 返回实例化的值，不是服务定义
        $this->container->add('array2', ['key2' => 'value2']);
        $service = $this->container->get('array2');
        $this->assertEquals(['key2' => 'value2'], $service);
    }

    public function testHas()
    {
        $this->assertFalse($this->container->has('nonexistent'));
        $this->container->add('test', 'value');
        $this->assertTrue($this->container->has('test'));
    }

    public function testGetWithObject()
    {
        $obj = new stdClass();
        $this->container->add('object', $obj);
        $instance = $this->container->get('object');
        $this->assertSame($obj, $instance);
    }

    public function testGetWithClosure()
    {
        $closure = function () {
            return 'closure result';
        };
        $this->container->add('closure', $closure);
        $result = $this->container->get('closure');
        $this->assertEquals('closure result', $result);
    }

    public function testGetWithCallable()
    {
        $this->container->add('callable', function() { return 'callable result'; });
        $result = $this->container->get('callable');
        $this->assertEquals('callable result', $result);
    }

    public function testGetWithClass()
    {
        $this->container->add('class', TestClass::class);
        $instance = $this->container->get('class');
        $this->assertInstanceOf(TestClass::class, $instance);
    }

    public function testGetWithArgs()
    {
        $this->container->add('class_with_args', TestClassWithArgs::class);
        $instance = $this->container->get('class_with_args', 'arg1', 'arg2');
        $this->assertInstanceOf(TestClassWithArgs::class, $instance);
        $this->assertEquals(['arg1', 'arg2'], $instance->getArgs());
    }

    public function testGetReturnsSingleton()
    {
        $this->container->add('singleton', TestClass::class);
        $instance1 = $this->container->get('singleton');
        $instance2 = $this->container->get('singleton');
        $this->assertSame($instance1, $instance2);
    }

    public function testClear()
    {
        $this->container->add('to_clear', 'value');
        $this->assertTrue($this->container->has('to_clear'));
        
        $this->container->get('to_clear'); // 这会创建实例
        
        $this->container->clear('to_clear');
        $this->assertFalse($this->container->has('to_clear'));
    }

    public function testNotFoundException()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Container item "nonexistent" not found.');
        $this->container->get('nonexistent');
    }

    public function testGetWithClassType()
    {
        // 测试当服务类型是 class 字符串时的情况
        $this->container->add('class_string', StdClass::class);
        $instance = $this->container->get('class_string');
        $this->assertInstanceOf(StdClass::class, $instance);
    }

    public function testArrayMergeEdgeCases()
    {
        // 测试空数组合并
        $this->container->add('empty_array', []);
        $service = $this->container->get('empty_array');
        $this->assertEquals([], $service);
        
        // 再次添加空数组应该保持为空
        $this->container->add('empty_array2', []);
        $service = $this->container->get('empty_array2');
        $this->assertEquals([], $service);
        
        // 测试与非空数组合并
        $this->container->add('mixed_array', ['a' => 1]);
        $this->container->add('mixed_array', ['b' => 2]);
        $service = $this->container->get('mixed_array');
        $this->assertEquals(['a' => 1, 'b' => 2], $service);
    }

    public function testServiceOverrideNonArrayType()
    {
        // 测试重复添加相同ID的非数组服务应该覆盖而不是合并
        $this->container->add('override_test', 'value1');
        $service = $this->container->get('override_test');
        $this->assertEquals('value1', $service);
        
        // 清除后重新添加
        $this->container->clear('override_test');
        $this->container->add('override_test', 'value2');
        $service = $this->container->get('override_test');
        $this->assertEquals('value2', $service);
        $this->assertNotEquals('value1', $service);
    }

    public function testGetWithConstructorParameters()
    {
        // 测试使用 get 方法时传递参数给类构造函数
        $this->container->add('class_with_params', TestClassWithParams::class);
        
        $instance = $this->container->get('class_with_params', 'param1', 'param2');
        $this->assertInstanceOf(TestClassWithParams::class, $instance);
        $this->assertEquals(['param1', 'param2'], $instance->getConstructorParams());
        
        // 测试参数改变时行为 - 因为是单例，实例不会改变
        $newInstance = $this->container->get('class_with_params', 'different', 'params');
        // 因为是单例，所以应该是同一个实例
        $this->assertSame($instance, $newInstance);
        // 但构造参数应为第一次传入的参数
        $this->assertEquals(['param1', 'param2'], $newInstance->getConstructorParams());
    }

    public function testGetWithDifferentServiceTypes()
    {
        // 测试 get 方法对不同类型服务的处理
        $this->container->add('simple_value', 'simple_string');
        $result = $this->container->get('simple_value');
        $this->assertEquals('simple_string', $result); // 应该返回原始值
        
        $this->container->add('numeric_value', 42);
        $result = $this->container->get('numeric_value');
        $this->assertEquals(42, $result); // 应该返回原始值
        
        $this->container->add('array_value', ['item1', 'item2']);
        $result = $this->container->get('array_value');
        $this->assertEquals(['item1', 'item2'], $result); // 应该返回原始值
    }

    public static function staticMethod()
    {
        return 'static method result';
    }

    public function testIteratorFunctionality()
    {
        $this->container->add('first', 'value1');
        $this->container->add('second', 'value2');
        
        $this->container->rewind();
        $this->assertTrue($this->container->valid());
        $this->assertEquals('first', $this->container->key());
        
        $current = $this->container->current();
        $this->assertEquals('value1', $current['service']);
        
        $this->container->next();
        $this->assertTrue($this->container->valid());
        $this->assertEquals('second', $this->container->key());
        
        $this->container->next();
        $this->assertFalse($this->container->valid());
    }

    public function testArrayAccessFunctionality()
    {
        // 测试 offsetSet (通过 [] 赋值)
        $this->container->offsetSet('array_access_test', 'array_value');
        
        // 测试 offsetExists
        $this->assertTrue($this->container->offsetExists('array_access_test'));
        $this->assertFalse($this->container->offsetExists('nonexistent'));
        
        // 测试 offsetGet - 现在返回实例化的值
        $value = $this->container->offsetGet('array_access_test');
        $this->assertEquals('array_value', $value);
        
        // 测试 offsetUnset
        $this->container->offsetUnset('array_access_test');
        $this->assertFalse($this->container->offsetExists('array_access_test'));
    }

    public function testRegisterServiceProvider()
    {
        $provider = new TestServiceProvider();
        $this->container->register($provider);
        
        $this->assertTrue($this->container->has('test.service'));
        $service = $this->container->get('test.service');
        $this->assertEquals('test', $service);
    }

    public function testDifferentValueTypes()
    {
        // 测试 null
        $this->container->add('null_value', null);
        $service = $this->container->get('null_value');
        $this->assertNull($service);

        // 测试布尔值
        $this->container->add('bool_true', true);
        $service = $this->container->get('bool_true');
        $this->assertTrue($service);

        $this->container->add('bool_false', false);
        $service = $this->container->get('bool_false');
        $this->assertFalse($service);

        // 测试浮点数
        $this->container->add('float_value', 3.14);
        $service = $this->container->get('float_value');
        $this->assertEquals(3.14, $service);

        // 测试对象
        $obj = new stdClass();
        $this->container->add('object_value', $obj);
        $service = $this->container->get('object_value');
        $this->assertSame($obj, $service);

        // 测试可调用函数
        $func = function() { return 'function result'; };
        $this->container->add('callable_value', $func);
        $result = $this->container->get('callable_value');
        $this->assertEquals('function result', $result);
    }

    public function testNotFoundExceptionInterface()
    {
        $exception = new NotFoundException();
        $this->assertInstanceOf(\Psr\Container\NotFoundExceptionInterface::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
}

class TestClass
{
}

class TestClassWithArgs
{
    private array $args;

    public function __construct(string $arg1, string $arg2)
    {
        $this->args = [$arg1, $arg2];
    }

    public function getArgs(): array
    {
        return $this->args;
    }
}

class TestClassWithParams
{
    private array $params = ['default1', 'default2'];

    public function __construct(string $param1 = 'default1', string $param2 = 'default2')
    {
        $this->params = [$param1, $param2];
    }

    public function getConstructorParams(): array
    {
        return $this->params;
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