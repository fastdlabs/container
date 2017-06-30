# Object Container

![Building](https://api.travis-ci.org/JanHuang/container.svg?branch=master)
[![Latest Stable Version](https://poser.pugx.org/fastd/container/v/stable)](https://packagist.org/packages/fastd/container) [![Total Downloads](https://poser.pugx.org/fastd/container/downloads)](https://packagist.org/packages/fastd/container) [![Latest Unstable Version](https://poser.pugx.org/fastd/container/v/unstable)](https://packagist.org/packages/fastd/container) [![License](https://poser.pugx.org/fastd/container/license)](https://packagist.org/packages/fastd/container)

Simple DI Container

### requirements

* PHP >=7.0

### installation

```
composer require "fastd/container" -vvv
```

### usage

#### using method injection

```php
$container = new FastD\Container\Container();

$container
    ->injectOn('date', new MethodInjection())
    ->withMethod('now')
    ->withArguments([
        new DateTime(),
    ]);

$obj = $container->get('date');

echo $obj->date; // (new DateTime())->format(DateTime::W3C)
```

#### using construct injection

```php
$container = new FastD\Container\Container();

$container
    ->injectOn('date', ConstructorInjection::class)
    ->withConstruct()
    ->withArguments([
        new DateTime(),
    ]);

$date = $container->get('date');

echo $obj->date; // (new DateTime())->format(DateTime::W3C)
```

#### using closure

```php
$container = new FastD\Container\Container();

$container->add('date', function () use ($container) {
    return new DateTime('now', $container->get('zone'));
});

$container->add('zone', function () {
    return new DateTimeZone('UTC');
});

$dateTime = $container->get('date');

echo $obj->date; // (new DateTime('now', new DateTimeZone("UTC")))->format(DateTime::W3C)
```

#### using DI

```php
$container = new FastD\Container\Container();

$container->add('zone', new DateTimeZone('UTC'));

$container->injectOn('date', function (DateTimeZone $dateTimeZone) {
    return new DateTime('now', $dateTimeZone);
});

$dateTimeZone = $container->get('date'); // new DateTime('now', new DateTimeZone('UTC'));
```

### Testing

```php
phpunit
```

## License MIT


