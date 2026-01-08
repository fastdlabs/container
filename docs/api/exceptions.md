# 异常类参考

FastD Container 使用标准的异常处理机制，主要的异常类继承自PSR-11标准接口。

## NotFoundException

```php
namespace FastD\Container;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
```

### 类层次结构

```
Exception
└── RuntimeException
    └── NotFoundException (实现 NotFoundExceptionInterface)
```

### 继承关系

- **继承**: `RuntimeException`
- **实现**: `Psr\Container\NotFoundExceptionInterface`

### 使用场景

当尝试获取不存在的服务时抛出此异常。

### 构造函数

```php
public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
```

参数与标准PHP Exception相同。

### 触发示例

```php
use FastD\Container\Container;
use FastD\Container\NotFoundException;

$container = new Container();

// 尝试获取不存在的服务
try {
    $service = $container->get('nonexistent.service');
} catch (NotFoundException $e) {
    echo "错误: " . $e->getMessage();
    // 输出: 错误: Container item "nonexistent.service" not found.
}
```

### 完整示例

```php
use FastD\Container\Container;
use FastD\Container\NotFoundException;

class ServiceManager
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getService(string $id)
    {
        try {
            return $this->container->got($id);
        } catch (NotFoundException $e) {
            // 记录错误日志
            error_log("Service not found: {$id}");
            
            // 可以选择返回默认服务或重新抛出异常
            throw new RuntimeException("Required service '{$id}' is not available", 0, $e);
        }
    }
}

// 使用示例
$container = new Container();
$manager = new ServiceManager($container);

try {
    $service = $manager->getService('database.connection');
} catch (RuntimeException $e) {
    echo "无法获取服务: " . $e->getMessage();
}
```

## 异常处理最佳实践

### 1. 具体异常捕获

```php
use FastD\Container\NotFoundException;
use Psr\Container\ContainerExceptionInterface;

try {
    $service = $container->got('some.service');
} catch (NotFoundException $e) {
    // 专门处理服务不存在的情况
    $this->handleMissingService($e->getMessage());
} catch (ContainerExceptionInterface $e) {
    // 处理其他容器异常
    $this->handleContainerError($e);
} catch (Exception $e) {
    // 处理其他所有异常
    $this->handleGeneralError($e);
}
```

### 2. 异常信息格式化

```php
class ContainerExceptionHandler
{
    public static function formatNotFoundException(NotFoundException $e): string
    {
        // 提取服务ID
        preg_match('/"([^"]+)"/', $e->getMessage(), $matches);
        $serviceId = $matches[1] ?? 'unknown';
        
        return sprintf(
            "服务 '%s' 未找到。请检查服务是否已正确注册。",
            $serviceId
        );
    }
    
    public static function logException(Exception $e): void
    {
        error_log(sprintf(
            "[%s] %s in %s:%d\n%s",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        ));
    }
}
```

### 3. 优雅降级

```php
class GracefulServiceLocator
{
    private Container $container;
    private array $fallbackServices;

    public function __construct(Container $container, array $fallbackServices = [])
    {
        $this->container = $container;
        $this->fallbackServices = $fallbackServices;
    }

    public function getService(string $id, $default = null)
    {
        try {
            return $this->container->got($id);
        } catch (NotFoundException $e) {
            // 尝试使用备用服务
            if (isset($this->fallbackServices[$id])) {
                return $this->fallbackServices[$id];
            }
            
            // 返回默认值
            if ($default !== null) {
                return $default;
            }
            
            // 重新抛出异常
            throw $e;
        }
    }
}

// 使用示例
$fallbacks = [
    'cache' => new NullCache(),
    'logger' => new NullLogger()
];

$locator = new GracefulServiceLocator($container, $fallbacks);
$cache = $locator->getService('cache'); // 即使未注册也会返回 NullCache
```

### 4. 异常转换

```php
class ExceptionConverter
{
    public static function convertToApplicationException(Exception $e): ApplicationException
    {
        if ($e instanceof NotFoundException) {
            return new ServiceNotFoundException(
                self::extractServiceId($e),
                $e->getCode(),
                $e
            );
        }
        
        if ($e instanceof ContainerExceptionInterface) {
            return new ContainerErrorException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
        
        return new ApplicationException(
            $e->getMessage(),
            $e->getCode(),
            $e
        );
    }
    
    private static function extractServiceId(NotFoundException $e): string
    {
        preg_match('/"([^"]+)"/', $e->getMessage(), $matches);
        return $matches[1] ?? 'unknown';
    }
}

// 使用示例
try {
    $service = $container->got('critical.service');
} catch (Exception $e) {
    $appException = ExceptionConverter::convertToApplicationException($e);
    throw $appException;
}
```

## 测试异常处理

### 单元测试示例

```php
use PHPUnit\Framework\TestCase;
use FastD\Container\Container;
use FastD\Container\NotFoundException;

class ContainerExceptionTest extends TestCase
{
    public function testNotFoundExceptionMessage()
    {
        $container = new Container();
        
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Container item "missing" not found.');
        
        $container->get('missing');
    }
    
    public function testExceptionInheritance()
    {
        $exception = new NotFoundException();
        
        $this->assertInstanceOf(\Psr\Container\NotFoundExceptionInterface::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }
    
    public function testExceptionWithCustomMessage()
    {
        $exception = new NotFoundException('Custom error message', 404);
        
        $this->assertEquals('Custom error message', $exception->getMessage());
        $this->assertEquals(404, $exception->getCode());
    }
}
```

### 集成测试示例

```php
class ServiceLocatorIntegrationTest extends TestCase
{
    private Container $container;
    
    protected function setUp(): void
    {
        $this->container = new Container();
    }
    
    public function testServiceResolutionWithDependencies()
    {
        // 注册依赖服务
        $this->container->add('config.database', [
            'host' => 'localhost',
            'port' => 3306
        ]);
        
        // 注册主服务
        $this->container->add('database.connection', function($container) {
            $config = $container->got('config.database');
            // 这里可能会抛出异常
            return new DatabaseConnection($config['host'], $config['port']);
        });
        
        // 测试正常情况
        $connection = $this->container->got('database.connection');
        $this->assertInstanceOf(DatabaseConnection::class, $connection);
        
        // 测试缺少依赖的情况
        $this->container->clear('config.database');
        
        $this->expectException(NotFoundException::class);
        $this->container->got('database.connection');
    }
}
```

## 调试技巧

### 1. 异常追踪

```php
function debugContainerException(NotFoundException $e, Container $container): void
{
    echo "=== 容器调试信息 ===\n";
    echo "异常消息: " . $e->getMessage() . "\n";
    echo "异常代码: " . $e->getCode() . "\n";
    echo "文件位置: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "调用堆栈:\n" . $e->getTraceAsString() . "\n";
    
    // 显示已注册的服务
    echo "已注册的服务:\n";
    foreach ($container as $id => $definition) {
        echo "  - {$id} ({$definition['type']})\n";
    }
}
```

### 2. 服务查找工具

```php
class ContainerDebugger
{
    public static function findSimilarServices(Container $container, string $searchTerm): array
    {
        $similar = [];
        foreach ($container as $id => $definition) {
            if (stripos($id, $searchTerm) !== false) {
                $similar[] = $id;
            }
        }
        return $similar;
    }
    
    public static function suggestServiceName(NotFoundException $e, Container $container): string
    {
        preg_match('/"([^"]+)"/', $e->getMessage(), $matches);
        $requested = $matches[1] ?? '';
        
        $similar = self::findSimilarServices($container, $requested);
        
        if (empty($similar)) {
            return "没有找到相似的服务名称。";
        }
        
        return "您是否想要找: " . implode(', ', array_slice($similar, 0, 3));
    }
}

// 使用示例
try {
    $service = $container->got('user.servce'); // 故意拼错
} catch (NotFoundException $e) {
    echo ContainerDebugger::suggestServiceName($e, $container);
    // 输出: 您是否想要找: user.service, user.repository
}
```

这些异常处理模式可以帮助您构建更加健壮和用户友好的应用程序。