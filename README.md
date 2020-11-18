# Container

![Building](https://api.travis-ci.org/JanHuang/container.svg?branch=master)
[![Latest Stable Version](https://poser.pugx.org/fastd/container/v/stable)](https://packagist.org/packages/fastd/container) 
[![Total Downloads](https://poser.pugx.org/fastd/container/downloads)](https://packagist.org/packages/fastd/container) 
[![Latest Unstable Version](https://poser.pugx.org/fastd/container/v/unstable)](https://packagist.org/packages/fastd/container) 
[![License](https://poser.pugx.org/fastd/container/license)](https://packagist.org/packages/fastd/container)

简单的PHP对象容器，DI组件已经独立维护: [di](https://github.com/fastdlabs/DI)

### Requirements

* PHP >=7.2

### Installation

```
composer require "fastd/container"
```

### Usage

```php
$container = new FastD\Container\Container();
$container->add('timezone', DateTimeZone::class);
$timezone = $this->container->get('timezone');
$this->assertInstanceOf(DateTimeZone::class, $timezone);
```

### Testing

```php
bin/phpunit
```

### 贡献

非常欢迎感兴趣，愿意参与其中，共同打造更好PHP生态，Swoole生态的开发者。

如果你乐于此，却又不知如何开始，可以试试下面这些事情：

* 在你的系统中使用，将遇到的问题 [反馈](https://github.com/JanHuang/fastD/issues)。
* 有更好的建议？欢迎联系 [bboyjanhuang@gmail.com](mailto:bboyjanhuang@gmail.com) 或 [新浪微博:编码侠](http://weibo.com/ecbboyjan)。

### 联系

如果你在使用中遇到问题，请联系: [bboyjanhuang@gmail.com](mailto:bboyjanhuang@gmail.com). 微博: [编码侠](http://weibo.com/ecbboyjan)

## License MIT
