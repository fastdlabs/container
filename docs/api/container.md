# Container 类

`FastD\Container\Container` 是FastD容器的核心类，实现了PSR-11标准接口以及额外的功能扩展。

## 类签名

```php
class Container implements \Psr\Container\ContainerInterface, \Iterator
```

## 属性

### `$services`
```php
protected array $services = []
```
存储所有注册的服务定义，格式为：
```php
[
    'service.id' => [
        'type' => 'string',    // 服务类型
        'service' => mixed     // 服务定义
    ]
]
```

### `$instances`
```php
protected array $instances = []
```
存储已创建的服务实例，用于单例模式：
```php
[
    'service.id' => object  // 已实例化的服务对象
]
```

## 构造函数

```php
public function __construct()
```

创建一个新的容器实例。

**示例**：
```php
$container = new FastD\Container\Container();
```

## 核心方法

### add()

```php
public function add(string $id, mixed $service): Container
```

注册一个服务到容器中。

**参数**：
- `string $id`: 服务标识符
- `mixed $service`: 服务定义（可以是类名、闭包、对象等）

**返回值**：
- `Container`: 返回容器实例，支持链式调用

**服务类型识别规则**：
1. `Closure` 实例 → `'closure'`
2. `object` → `'object'`  
3. 字符串且类存在 → `'class'`
4. 可调用 → `'callable'`
5. 其他 → `gettype()` 结果

**数组合并行为**：
- 如果服务类型为数组且已存在同名服务，则自动合并数组
- 其他类型会直接覆盖原有服务

**示例**：
```php
// 注册类
$container->add('logger', Monolog\Logger::class);

// 注册闭包
$container->add('database', function() {
    return new DatabaseConnection();
});

// 注册对象实例
$container->add('config', new Config(['debug' => true]));

// 注册数组（会合并）
$container->add('settings', ['timezone' => 'UTC']);
$container->add('settings', ['locale' => 'zh_CN']);
// 结果: ['timezone' => 'UTC', 'locale' => 'zh_CN']
```

### has()

```php
public function has(string $id): bool
```

检查指定服务是否存在。

**参数**：
- `string $id`: 服务标识符

**返回值**：
- `bool`: 服务存在返回 `true`，否则返回 `false`

**示例**：
```php
if ($container->has('logger')) {
    $logger = $container->got('logger');
}
```

### get()

```php
public function get(string $id): mixed
```

获取服务的定义信息（不是实例）。

**参数**：
- `string $id`: 服务标识符

**返回值**：
- `mixed`: 服务定义数组，格式为 `['type' => string, 'service' => mixed]`

**异常**：
- `NotFoundException`: 当服务不存在时抛出

**注意**：这是PSR-11标准方法，返回服务定义而非实例。要获取实例请使用 `got()` 方法。

**示例**：
```php
$definition = $container->get('logger');
// 返回: ['type' => 'class', 'service' => 'Monolog\Logger']

// 获取实际实例
$instance = $container->got('logger');
```

### got()

```php
public function got(string $id, ...$parameters): mixed
```

获取服务的实际实例对象。

**参数**：
- `string $id`: 服务标识符
- `...$parameters`: 可选的构造函数参数

**返回值**：
- `mixed`: 服务实例对象

**单例行为**：
- 第一次调用时创建实例并缓存
- 后续调用返回缓存的实例
- 参数只在首次创建时使用

**示例**：
```php
// 简单获取
$logger = $container->got('logger');

// 带参数获取（用于构造函数）
$repository = $container->got('user.repository', $entityManager);

// 单例行为
$instance1 = $container->got('service');
$instance2 = $container->got('service');
var_dump($instance1 === $instance2); // true
```

### clear()

```php
public function clear(string $id): void
```

从容器中移除指定服务及其缓存实例。

**参数**：
- `string $id`: 服务标识符

**示例**：
```php
$container->clear('logger');
// 服务定义和服务实例都被移除
```

## ArrayAccess 接口实现

### offsetExists()

```php
public function offsetExists(mixed $offset): bool
```

检查偏移量（服务ID）是否存在。对应 `has()` 方法。

**示例**：
```php
isset($container['logger']); // 等同于 $container->has('logger')
```

### offsetGet()

```php
public function offsetGet(mixed $offset): mixed
```

获取指定偏移量的服务定义。对应 `get()` 方法。

**示例**：
```php
$definition = $container['logger']; // 等同于 $container->get('logger')
```

### offsetSet()

```php
public function offsetSet(mixed $offset, mixed $value): void
```

设置指定偏移量的服务。对应 `add()` 方法。

**示例**：
```php
$container['logger'] = Monolog\Logger::class; // 等同于 $container->add('logger', ...)
```

### offsetUnset()

```php
public function offsetUnset(mixed $offset): void
```

移除指定偏移量的服务。对应 `clear()` 方法。

**示例**：
```php
unset($container['logger']); // 等同于 $container->clear('logger')
```

## Iterator 接口实现

### current()

```php
public function current(): mixed
```

返回当前服务定义。

### next()

```php
public function next(): void
```

移动到下一个服务。

### key()

```php
public function key(): mixed
```

返回当前服务ID。

### valid()

```php
public function valid(): bool
```

检查当前位置是否有效。

### rewind()

```php
public function rewind(): void
```

重置迭代器到第一个服务。

**遍历示例**：
```php
foreach ($container as $id => $definition) {
    echo "Service ID: {$id}\n";
    echo "Type: {$definition['type']}\n";
    echo "Definition: " . print_r($definition['service'], true) . "\n";
}
```

## 服务提供者支持

### register()

```php
public function register(ServiceProviderInterface $registrar): void
```

注册服务提供者。

**参数**：
- `ServiceProviderInterface $registrar`: 服务提供者实例

**示例**：
```php
class DatabaseServiceProvider implements ServiceProviderInterface 
{
    public function register(Container $container): void
    {
        $container->add('database.config', ['host' => 'localhost']);
        $container->add('database.connection', function($container) {
            $config = $container->got('database.config');
            return new PDO("mysql:host={$config['host']}");
        });
    }
}

$provider = new DatabaseServiceProvider();
$container->register($provider);
```

## 使用示例

### 完整使用场景

```php
use FastD\Container\Container;

// 创建容器
$container = new Container();

// 1. 基础服务注册
$container->add('config', [
    'database' => [
        'host' => 'localhost',
        'port' => 3306
    ]
]);

// 2. 闭包服务
$container->add('logger', function() {
    return new Monolog\Logger('app');
});

// 3. 类服务
$container->add('database', PDO::class);

// 4. 对象实例
$cache = new Redis();
$container->add('cache', $cache);

// 5. 使用服务
$config = $container->got('config');
$logger = $container->got('logger');
$db = $container->got('database', 'mysql:host=localhost');

// 6. 数组合并
$container->add('app.settings', ['debug' => false]);
$container->add('app.settings', ['timezone' => 'UTC']);

$settings = $container->got('app.settings');
// 结果: ['debug' => false, 'timezone' => 'UTC']

// 7. 数组访问
$container['session'] = SessionHandler::class;
if (isset($container['session'])) {
    $session = $container['session'];
}

// 8. 遍历服务
foreach ($container as $id => $definition) {
    echo "Registered service: {$id} ({$definition['type']})\n";
}
```

## 注意事项

1. **get() vs got()**: `get()` 返回定义信息，`got()` 返回实际实例
2. **单例模式**: `got()` 方法具有单例行为，多次调用返回同一实例
3. **参数传递**: 构造函数参数只在首次创建实例时生效
4. **数组合并**: 只有数组类型的服务才会被合并，其他类型会覆盖
5. **性能考虑**: 对象类型服务会立即缓存，避免重复创建