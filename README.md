# Object Container

![Building](https://api.travis-ci.org/JanHuang/container.svg?branch=master)
[![Latest Stable Version](https://poser.pugx.org/fastd/container/v/stable)](https://packagist.org/packages/fastd/container) [![Total Downloads](https://poser.pugx.org/fastd/container/downloads)](https://packagist.org/packages/fastd/container) [![Latest Unstable Version](https://poser.pugx.org/fastd/container/v/unstable)](https://packagist.org/packages/fastd/container) [![License](https://poser.pugx.org/fastd/container/license)](https://packagist.org/packages/fastd/container)

简单的 PHP 对象存储容器。

## 要求

* PHP 7+

## Composer 

```json
composer require "fastd/container:3.0.x-dev" -vvv
```

## 使用

```php
use FastD\Container\Container;
use FastD\Container\Tests\Services\A;
use FastD\Container\Tests\Services\B;
use FastD\Container\Tests\Services\C;
use FastD\Container\Tests\Services\D;

$container = new Container([
    A::class, B::class, C::class, D::class,
    'a' => A::class,
]);

$a = $container->get('a');

$b = $container->get(B::class);

$instance = $b->instance([10]);
```

通过构造的时候添加不同的对象，也可以在实例化后通过 `::set($name, $class)` 方法进行设置。

往容器中注入对象后(注入对象后只是将对象的类名进行存储，获取的时候才会对其实例化)，即可以通过 `::get($name)` 方法获取对象。

获取对象后，对象是一个 `FastD\Container\Service`，`Service` 支持对象依赖注入等方法，支持单例化，实例化。

实例化: `FastD\Container\Service::instance(array $parameters = [])` 参数顺序和 `call_user_func_array` 顺序保持一致。

单例化: `FastD\Container\Service::singleton(array $parameters = [])` 保持对象单例。

对象依赖注入，支持所有方法依赖注入(除了部分魔术方法外)。可以查看上述 `B` 对象

具体操作可以参考上述事例。

## Testing

```php
phpunit
```

## License MIT


