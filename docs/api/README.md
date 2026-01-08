# API 参考

本节详细介绍 FastD Container 的所有公共API，包括类、方法、接口及其使用方式。

## 目录

- [Container 类](container.md) - 主容器类的完整API参考
- [ServiceProviderInterface](service-provider.md) - 服务提供者接口
- [NotFoundException](exceptions.md) - 异常类参考

## 快速概览

### 基本使用模式

```php
use FastD\Container\Container;

// 创建容器实例
$container = new Container();

// 注册服务
$container->add('database', DatabaseConnection::class);

// 获取服务实例
$db = $container->got('database');

// 检查服务是否存在
if ($container->has('database')) {
    // 服务存在
}
```

### 核心概念

#### 服务注册 vs 服务获取

FastD Container 区分两个不同的操作：

- **服务注册**: 使用 `add()` 方法将服务定义存储到容器中
- **服务获取**: 分为两种方式：
  - `get()`: 返回服务定义信息（数组格式）
  - `got()`: 返回实际的服务实例对象

#### 服务类型

容器支持多种服务类型：

| 类型 | 示例 | 说明 |
|------|------|------|
| 类名字符串 | `'App\Service'` | 容器会创建该类的新实例 |
| 闭包 | `fn() => new Service()` | 执行闭包返回结果 |
| 对象实例 | `new Service()` | 直接存储对象引用 |
| 可调用数组 | `[$obj, 'method']` | 调用指定方法 |
| 基础类型 | `'string'`, `123`, `[]` | 直接存储值 |

## PSR-11 兼容性

FastD Container 完全实现 PSR-11 Container Interface：

```php
use Psr\Container\ContainerInterface;

// Container 实现了 ContainerInterface
$container instanceof ContainerInterface; // true

// 可以使用标准的PSR-11方法
$container->has('service');  // 检查服务存在性
$container->get('service');  // 获取服务（注意：返回定义而非实例）
```

**注意**: PSR-11 的 `get()` 方法在 FastD Container 中返回服务定义数组，要获取实际实例请使用 `got()` 方法。

## ArrayAccess 和 Iterator 支持

容器实现了PHP标准库接口：

### ArrayAccess 接口

```php
// 设置服务（相当于 add()）
$container['logger'] = Logger::class;

// 检查服务存在性（相当于 has()）
isset($container['logger']); // true

// 获取服务定义（相当于 get()）
$definition = $container['logger'];

// 删除服务（相当于 clear()）
unset($container['logger']);
```

### Iterator 接口

```php
// 遍历所有注册的服务
foreach ($container as $id => $definition) {
    echo "Service: {$id}\n";
    echo "Type: {$definition['type']}\n";
}
```

## 错误处理

容器使用标准的异常处理机制：

```php
use FastD\Container\NotFoundException;

try {
    $service = $container->get('nonexistent');
} catch (NotFoundException $e) {
    // 服务不存在
    echo $e->getMessage(); // "Container item "nonexistent" not found."
}
```

## 最佳实践

### 1. 服务命名约定

```php
// 推荐：使用点号分隔命名空间
$container->add('database.connection', $connection);
$container->add('cache.redis', $redis);

// 避免：过于简单或复杂的名称
$container->add('db', $connection);        // 太简单
$container->add('Database_Connection', $connection); // 太复杂
```

### 2. 依赖注入模式

```php
// 推荐：在服务提供者中集中配置
class DatabaseServiceProvider implements ServiceProviderInterface 
{
    public function register(Container $container): void
    {
        $container->add('database.config', [
            'host' => 'localhost',
            'port' => 3306
        ]);
        
        $container->add('database.connection', function() use ($container) {
            $config = $container->got('database.config');
            return new DatabaseConnection($config);
        });
    }
}
```

### 3. 单例管理

```php
// 服务实例会被自动缓存
$userService = $container->got('user.service');
$sameUserService = $container->got('user.service'); // 同一实例

// 如需重新创建实例，先清除缓存
$container->clear('user.service');
$newUserService = $container->got('user.service'); // 新实例
```

### 4. 参数传递

```php
// 向构造函数传递参数
$container->add('user.repository', UserRepository::class);
$repository = $container->got('user.repository', $entityManager, $logger);

// 向闭包传递参数
$container->add('factory', function($config, $logger) {
    return new Factory($config, $logger);
});
$factory = $container->got('factory', $config, $logger);
```