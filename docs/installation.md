# 安装与使用

本章节详细介绍 FastD Container 的安装配置、快速入门和最佳实践。

## 系统要求

### PHP 版本
- **最低要求**: PHP 8.2 或更高版本
- **推荐版本**: PHP 8.3+

### 依赖扩展
FastD Container 本身不依赖额外的PHP扩展，但某些使用场景可能需要：

- `json` - JSON配置文件解析
- `pdo` - 数据库相关服务
- `redis` - Redis缓存服务

## 安装方式

### Composer 安装（推荐）

```bash
composer require fastd/container
```

### 手动安装

1. 下载源码到项目目录
2. 在项目中配置自动加载：

```json
{
    "autoload": {
        "psr-4": {
            "FastD\\Container\\": "path/to/container/src/"
        }
    }
}
```

3. 运行 `composer dump-autoload`

## 快速入门

### 基础使用

```php
<?php

require_once 'vendor/autoload.php';

use FastD\Container\Container;

// 1. 创建容器实例
$container = new Container();

// 2. 注册服务
$container->add('logger', Monolog\Logger::class);
$container->add('config', [
    'app_name' => 'My Application',
    'debug' => true
]);

// 3. 获取服务实例
$logger = $container->got('logger');
$config = $container->got('config');

echo $config['app_name']; // 输出: My Application
```

### Web应用集成示例

```php
<?php

use FastD\Container\Container;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Application
{
    private Container $container;
    
    public function __construct()
    {
        $this->container = new Container();
        $this->registerServices();
    }
    
    private function registerServices(): void
    {
        // 配置服务
        $this->container->add('config', [
            'database' => [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'name' => $_ENV['DB_NAME'] ?? 'myapp'
            ]
        ]);
        
        // 数据库服务
        $this->container->add('database', function($container) {
            $config = $container->got('config')['database'];
            return new PDO(
                "mysql:host={$config['host']};dbname={$config['name']}",
                $_ENV['DB_USER'],
                $_ENV['DB_PASS']
            );
        });
        
        // 路由服务
        $this->container->add('router', function($container) {
            return new Router($container->got('logger'));
        });
    }
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $this->container->got('router')->match($request);
        $handler = $this->container->got($route->getHandler());
        
        return $handler->handle($request);
    }
}
```

## 配置管理

### 基础配置

```php
use FastD\Container\Container;

$container = new Container();

// 简单配置
$container->add('app.config', [
    'name' => 'My App',
    'version' => '1.0.0',
    'debug' => true
]);

// 环境相关配置
$container->add('env.config', [
    'development' => [
        'log_level' => 'debug',
        'cache_driver' => 'file'
    ],
    'production' => [
        'log_level' => 'error',
        'cache_driver' => 'redis'
    ]
]);

// 动态配置
$container->add('dynamic.config', function() {
    return [
        'timestamp' => time(),
        'memory_usage' => memory_get_usage(),
        'loaded_extensions' => get_loaded_extensions()
    ];
});
```

### 配置文件加载

```php
class ConfigLoader
{
    private Container $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function loadFromFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("Config file not found: {$filePath}");
        }
        
        $config = require $filePath;
        $this->container->add('app.config', $config);
    }
    
    public function loadFromDirectory(string $directory): void
    {
        $files = glob($directory . '/*.php');
        $config = [];
        
        foreach ($files as $file) {
            $key = basename($file, '.php');
            $config[$key] = require $file;
        }
        
        $this->container->add('app.config', $config);
    }
}

// 使用示例
$loader = new ConfigLoader($container);
$loader->loadFromDirectory(__DIR__ . '/config');
```

## 服务注册模式

### 直接注册

```php
// 注册具体的类
$container->add('user.repository', UserRepository::class);

// 注册闭包
$container->add('mailer', function() {
    return new Mailer($_ENV['SMTP_HOST']);
});

// 注册预创建的对象
$cache = new RedisCache();
$container->add('cache', $cache);
```

### 批量注册

```php
class ServiceRegistrar
{
    private Container $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function registerMultiple(array $services): void
    {
        foreach ($services as $id => $service) {
            $this->container->add($id, $service);
        }
    }
}

// 使用示例
$registrar = new ServiceRegistrar($container);
$registrar->registerMultiple([
    'logger' => Monolog\Logger::class,
    'cache' => RedisCache::class,
    'database' => DatabaseConnection::class
]);
```

## 依赖注入

### 构造函数注入

```php
class UserService
{
    private UserRepository $repository;
    private Logger $logger;
    
    public function __construct(UserRepository $repository, Logger $logger)
    {
        $this->repository = $repository;
        $this->logger = $logger;
    }
    
    public function getUser(int $id): ?User
    {
        $this->logger->info("Fetching user {$id}");
        return $this->repository->findById($id);
    }
}

// 容器配置
$container->add('user.repository', UserRepository::class);
$container->add('logger', Monolog\Logger::class);
$container->add('user.service', function($container) {
    return new UserService(
        $container->got('user.repository'),
        $container->got('logger')
    );
});

// 使用服务
$userService = $container->got('user.service');
$user = $userService->getUser(123);
```

### Setter注入

```php
class OrderService
{
    private ?PaymentGateway $paymentGateway = null;
    private ?EmailService $emailService = null;
    
    public function setPaymentGateway(PaymentGateway $gateway): void
    {
        $this->paymentGateway = $gateway;
    }
    
    public function setEmailService(EmailService $email): void
    {
        $this->emailService = $email;
    }
    
    public function processOrder(Order $order): bool
    {
        // 处理订单逻辑
        return true;
    }
}

// 容器配置
$container->add('payment.gateway', StripeGateway::class);
$container->add('email.service', SendGridService::class);
$container->add('order.service', function($container) {
    $service = new OrderService();
    $service->setPaymentGateway($container->got('payment.gateway'));
    $service->setEmailService($container->got('email.service'));
    return $service;
});
```

### 工厂模式

```php
class ServiceFactory
{
    private Container $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function createDatabaseConnection(): PDO
    {
        $config = $this->container->got('database.config');
        return new PDO(
            "mysql:host={$config['host']};dbname={$config['name']}",
            $config['username'],
            $config['password']
        );
    }
    
    public function createUserRepository(): UserRepository
    {
        return new UserRepository(
            $this->createDatabaseConnection(),
            $this->container->got('logger')
        );
    }
}

// 容器配置
$container->add('service.factory', function($container) {
    return new ServiceFactory($container);
});

$container->add('user.repository', function($container) {
    return $container->got('service.factory')->createUserRepository();
});
```

## 高级用法

### 服务装饰器

```php
interface CacheInterface
{
    public function get(string $key);
    public function set(string $key, $value, int $ttl = 3600);
}

class RedisCache implements CacheInterface
{
    public function get(string $key) { /* ... */ }
    public function set(string $key, $value, int $ttl = 3600) { /* ... */ }
}

class CacheDecorator implements CacheInterface
{
    private CacheInterface $cache;
    private Logger $logger;
    
    public function __construct(CacheInterface $cache, Logger $logger)
    {
        $this->cache = $cache;
        $this->logger = $logger;
    }
    
    public function get(string $key)
    {
        $this->logger->info("Cache GET: {$key}");
        return $this->cache->get($key);
    }
    
    public function set(string $key, $value, int $ttl = 3600)
    {
        $this->logger->info("Cache SET: {$key}");
        return $this->cache->set($key, $value, $ttl);
    }
}

// 容器配置
$container->add('cache.real', RedisCache::class);
$container->add('logger', Monolog\Logger::class);
$container->add('cache', function($container) {
    return new CacheDecorator(
        $container->got('cache.real'),
        $container->got('logger')
    );
});
```

### 条件服务注册

```php
class ConditionalServiceRegistrar
{
    private Container $container;
    private array $environment;
    
    public function __construct(Container $container, array $environment = [])
    {
        $this->container = $container;
        $this->environment = $environment;
    }
    
    public function registerServices(): void
    {
        // 根据环境注册不同的服务
        if ($this->environment['APP_ENV'] === 'production') {
            $this->container->add('cache.driver', RedisCache::class);
            $this->container->add('logger.handler', SyslogHandler::class);
        } else {
            $this->container->add('cache.driver', ArrayCache::class);
            $this->container->add('logger.handler', StreamHandler::class);
        }
        
        // 根据功能开关注册服务
        if ($this->environment['FEATURE_EMAIL'] ?? false) {
            $this->container->add('email.service', SendGridService::class);
        }
        
        // 根据配置注册服务
        $dbEnabled = $this->environment['DATABASE_ENABLED'] ?? true;
        if ($dbEnabled) {
            $this->container->add('database', DatabaseConnection::class);
        }
    }
}

// 使用示例
$registrar = new ConditionalServiceRegistrar($container, $_ENV);
$registrar->registerServices();
```

## 测试集成

### 单元测试配置

```php
use PHPUnit\Framework\TestCase;
use FastD\Container\Container;

class ServiceTestCase extends TestCase
{
    protected Container $container;
    
    protected function setUp(): void
    {
        $this->container = new Container();
        $this->setUpContainer();
    }
    
    protected function setUpContainer(): void
    {
        // 注册测试专用的服务
        $this->container->add('logger', TestLogger::class);
        $this->container->add('database', TestDatabase::class);
        $this->container->add('config', [
            'test_mode' => true,
            'fixtures_path' => __DIR__ . '/fixtures'
        ]);
    }
    
    protected function getService(string $id)
    {
        return $this->container->got($id);
    }
}

class UserServiceTest extends ServiceTestCase
{
    public function testCreateUser()
    {
        $userService = $this->getService('user.service');
        $user = $userService->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->getName());
    }
}
```

### Mock服务

```php
use PHPUnit\Framework\MockObject\MockBuilder;

class MockServiceHelper
{
    private Container $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function mockService(string $id, $mock): void
    {
        $this->container->add($id, $mock);
    }
    
    public function mockDatabaseResults(array $results): void
    {
        $mockDb = $this->createMock(DatabaseInterface::class);
        $mockDb->expects($this->any())
               ->method('query')
               ->willReturn($results);
               
        $this->mockService('database', $mockDb);
    }
    
    public function mockHttpClient(array $responses): void
    {
        $mockClient = $this->createMock(HttpClientInterface::class);
        $mockClient->expects($this->any())
                   ->method('request')
                   ->willReturnCallback(function($method, $url) use ($responses) {
                       return $responses[$url] ?? null;
                   });
                   
        $this->mockService('http.client', $mockClient);
    }
}
```

## 性能优化

### 服务预加载

```php
class ServicePreloader
{
    private Container $container;
    private array $preloadList;
    
    public function __construct(Container $container, array $preloadList = [])
    {
        $this->container = $container;
        $this->preloadList = $preloadList;
    }
    
    public function preload(): void
    {
        foreach ($this->preloadList as $serviceId) {
            if ($this->container->has($serviceId)) {
                $this->container->got($serviceId);
            }
        }
    }
}

// 使用示例
$preloader = new ServicePreloader($container, [
    'database.connection',
    'logger',
    'cache.driver'
]);

$preloader->preload(); // 预加载关键服务
```

### 缓存容器状态

```php
class ContainerCache
{
    private string $cacheFile;
    
    public function __construct(string $cacheFile = '/tmp/container_cache.php')
    {
        $this->cacheFile = $cacheFile;
    }
    
    public function save(Container $container): void
    {
        $services = [];
        foreach ($container as $id => $definition) {
            // 只缓存可序列化的服务定义
            if ($this->isSerializable($definition)) {
                $services[$id] = $definition;
            }
        }
        
        file_put_contents($this->cacheFile, '<?php return ' . var_export($services, true) . ';');
    }
    
    public function load(Container $container): bool
    {
        if (!file_exists($this->cacheFile)) {
            return false;
        }
        
        $cachedServices = require $this->cacheFile;
        
        foreach ($cachedServices as $id => $definition) {
            $container->add($id, $definition['service']);
        }
        
        return true;
    }
    
    private function isSerializable(array $definition): bool
    {
        return !in_array($definition['type'], ['closure', 'object']);
    }
}
```

## 部署注意事项

### 生产环境配置

```php
class ProductionContainerSetup
{
    public static function configure(Container $container): void
    {
        // 启用服务缓存
        $container->add('cache.enabled', true);
        
        // 设置生产环境配置
        $container->add('app.env', 'production');
        
        // 注册生产专用服务
        $container->add('error.handler', ProductionErrorHandler::class);
        $container->add('logger', function() {
            return new Monolog\Logger('app', [
                new Monolog\Handler\SyslogHandler('app')
            ]);
        });
        
        // 禁用调试服务
        $container->add('debug.mode', false);
    }
}
```

### 开发环境配置

```php
class DevelopmentContainerSetup
{
    public static function configure(Container $container): void
    {
        // 启用调试服务
        $container->add('debug.mode', true);
        $container->add('debug.bar', DebugBar::class);
        
        // 注册开发专用服务
        $container->add('logger', function() {
            return new Monolog\Logger('app', [
                new Monolog\Handler\StreamHandler('php://stderr')
            ]);
        });
        
        // 启用详细错误报告
        $container->add('error.handler', DevelopmentErrorHandler::class);
    }
}
```

这些配置和使用模式可以帮助您更好地在项目中集成和使用FastD Container。