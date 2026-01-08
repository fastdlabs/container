# ServiceProviderInterface

`FastD\Container\ServiceProviderInterface` 是服务提供者的标准接口，用于批量注册相关服务。

## 接口定义

```php
namespace FastD\Container;

interface ServiceProviderInterface
{
    public function register(Container $container): void;
}
```

## 方法

### register()

```php
public function register(Container $container): void
```

在此方法中注册相关的服务到容器中。

**参数**：
- `Container $container`: 容器实例

**返回值**：
- `void`: 无返回值

## 使用场景

服务提供者主要用于以下场景：

1. **模块化服务注册** - 将相关的服务组织在一起
2. **延迟加载** - 只在需要时注册服务
3. **配置管理** - 集中管理服务的配置和依赖关系
4. **第三方集成** - 为第三方库提供标准化的集成方式

## 实现示例

### 基础数据库服务提供者

```php
use FastD\Container\Container;
use FastD\Container\ServiceProviderInterface;

class DatabaseServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // 注册配置
        $container->add('database.config', [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'myapp',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4'
        ]);

        // 注册连接工厂
        $container->add('database.factory', function($container) {
            return new DatabaseConnectionFactory(
                $container->got('database.config')
            );
        });

        // 注册主连接
        $container->add('database.connection', function($container) {
            return $container->got('database.factory')->createConnection();
        });

        // 注册查询构建器
        $container->add('database.query_builder', function($container) {
            return new QueryBuilder(
                $container->got('database.connection')
            );
        });
    }
}
```

### 缓存服务提供者

```php
class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // 缓存配置
        $container->add('cache.config', [
            'default' => 'redis',
            'stores' => [
                'redis' => [
                    'driver' => 'redis',
                    'host' => '127.0.0.1',
                    'port' => 6379,
                    'database' => 0
                ],
                'file' => [
                    'driver' => 'file',
                    'path' => '/tmp/cache'
                ]
            ]
        ]);

        // Redis缓存驱动
        $container->add('cache.drivers.redis', function($container) {
            $config = $container->got('cache.config');
            $redisConfig = $config['stores']['redis'];
            
            $redis = new Redis();
            $redis->connect($redisConfig['host'], $redisConfig['port']);
            $redis->select($redisConfig['database']);
            
            return new RedisCacheDriver($redis);
        });

        // 文件缓存驱动
        $container->add('cache.drivers.file', function($container) {
            $config = $container->got('cache.config');
            $fileConfig = $config['stores']['file'];
            
            return new FileCacheDriver($fileConfig['path']);
        });

        // 缓存管理器
        $container->add('cache.manager', function($container) {
            $config = $container->got('cache.config');
            $drivers = [
                'redis' => $container->got('cache.drivers.redis'),
                'file' => $container->got('cache.drivers.file')
            ];
            
            return new CacheManager($drivers, $config['default']);
        });

        // 默认缓存实例
        $container->add('cache', function($container) {
            return $container->got('cache.manager')->driver();
        });
    }
}
```

### 日志服务提供者

```php
class LoggingServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // 日志配置
        $container->add('logging.config', [
            'default' => 'stack',
            'channels' => [
                'single' => [
                    'driver' => 'single',
                    'path' => '/var/log/app.log',
                    'level' => 'debug'
                ],
                'daily' => [
                    'driver' => 'daily',
                    'path' => '/var/log/app.log',
                    'days' => 7
                ]
            ]
        ]);

        // 单文件日志处理器
        $container->add('logging.handlers.single', function($container) {
            $config = $container->got('logging.config')['channels']['single'];
            return new StreamHandler($config['path'], $config['level']);
        });

        // 每日轮转日志处理器
        $container->add('logging.handlers.daily', function($container) {
            $config = $container->got('logging.config')['channels']['daily'];
            return new RotatingFileHandler($config['path'], $config['days']);
        });

        // 日志通道工厂
        $container->add('logging.factory', function($container) {
            return new LoggerFactory(
                $container->got('logging.handlers.single'),
                $container->got('logging.handlers.daily')
            );
        });

        // 主日志实例
        $container->add('logger', function($container) {
            $config = $container->got('logging.config');
            $factory = $container->got('logging.factory');
            return $factory->channel($config['default']);
        });
    }
}
```

## 高级用法

### 条件注册

```php
class ConditionalServiceProvider implements ServiceProviderInterface
{
    private bool $enableFeature;

    public function __construct(bool $enableFeature = true)
    {
        $this->enableFeature = $enableFeature;
    }

    public function register(Container $container): void
    {
        if ($this->enableFeature) {
            $container->add('feature.service', FeatureService::class);
        }
        
        // 始终注册基础服务
        $container->add('base.service', BaseService::class);
    }
}
```

### 依赖其他服务提供者

```php
class ApplicationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // 注册依赖的服务提供者
        $container->register(new DatabaseServiceProvider());
        $container->register(new CacheServiceProvider());
        $container->register(new LoggingServiceProvider());

        // 注册应用特定服务
        $container->add('app.router', function($container) {
            return new Router(
                $container->got('logger'),
                $container->got('cache')
            );
        });

        $container->add('app.dispatcher', function($container) {
            return new Dispatcher(
                $container->got('app.router'),
                $container->got('logger')
            );
        });
    }
}
```

### 动态配置服务提供者

```php
class ConfigurableServiceProvider implements ServiceProviderInterface
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'prefix' => 'app',
            'middleware' => [],
            'bindings' => []
        ], $config);
    }

    public function register(Container $container): void
    {
        $prefix = $this->config['prefix'];

        // 动态注册服务
        foreach ($this->config['bindings'] as $abstract => $concrete) {
            $container->add("{$prefix}.{$abstract}", $concrete);
        }

        // 注册中间件堆栈
        $container->add("{$prefix}.middleware", function($container) {
            $stack = [];
            foreach ($this->config['middleware'] as $middleware) {
                $stack[] = $container->got($middleware);
            }
            return new MiddlewareStack($stack);
        });
    }
}

// 使用示例
$serviceProvider = new ConfigurableServiceProvider([
    'prefix' => 'api',
    'middleware' => ['auth.middleware', 'cors.middleware'],
    'bindings' => [
        'controller.user' => UserController::class,
        'controller.post' => PostController::class
    ]
]);

$container->register($serviceProvider);
```

## 最佳实践

### 1. 职责分离

```php
// 好的做法：每个服务提供者职责单一
class UserServiceProvider implements ServiceProviderInterface { /* ... */ }
class OrderServiceProvider implements ServiceProviderInterface { /* ... */ }
class NotificationServiceProvider implements ServiceProviderInterface { /* ... */ }

// 避免：一个提供者做太多事情
class EverythingServiceProvider implements ServiceProviderInterface { /* ... */ }
```

### 2. 依赖声明

```php
class OrderServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // 明确声明依赖关系
        $container->add('order.repository', function($container) {
            return new OrderRepository(
                $container->got('database.connection'),  // 依赖数据库
                $container->got('logger')               // 依赖日志
            );
        });

        $container->add('order.service', function($container) {
            return new OrderService(
                $container->got('order.repository'),    // 依赖仓储
                $container->got('payment.service')      // 依赖支付服务
            );
        });
    }
}
```

### 3. 配置外部化

```php
class ExternalConfigServiceProvider implements ServiceProviderInterface
{
    private string $configPath;

    public function __construct(string $configPath)
    {
        $this->configPath = $configPath;
    }

    public function register(Container $container): void
    {
        // 从外部配置文件加载
        $config = require $this->configPath;
        
        $container->add('app.config', $config);
        
        // 基于配置注册服务
        if ($config['cache']['enabled']) {
            $container->add('cache', $config['cache']['driver']);
        }
    }
}
```

### 4. 延迟注册

```php
class LazyServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // 只注册工厂，实际创建延迟到使用时
        $container->add('heavy.service', function() {
            // 复杂的初始化逻辑在这里
            return new HeavyService();
        });
        
        // 轻量级服务立即注册
        $container->add('light.service', new LightService());
    }
}
```

## 与其他组件的关系

服务提供者通常与以下组件配合使用：

1. **配置管理器** - 加载和管理服务配置
2. **路由系统** - 注册控制器和中间件
3. **事件系统** - 注册事件监听器
4. **队列系统** - 注册作业处理器

这种设计模式使得应用程序更加模块化和可维护。