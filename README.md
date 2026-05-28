# FastD Container

![Building](https://api.travis-ci.org/JanHuang/container.svg?branch=master)
[![Latest Stable Version](https://poser.pugx.org/fastd/container/v/stable)](https://packagist.org/packages/fastd/container) 
[![Total Downloads](https://poser.pugx.org/fastd/container/downloads)](https://packagist.org/packages/fastd/container) 
[![Latest Unstable Version](https://poser.pugx.org/fastd/container/v/unstable)](https://packagist.org/packages/fastd/container) 
[![License](https://poser.pugx.org/fastd/container/license)](https://packagist.org/packages/fastd/container)

FastD Container 是一个轻量级但功能强大的PHP依赖注入容器，完全实现PSR-11标准接口。它提供了简洁易用的API来管理应用程序中的服务依赖关系，支持多种服务类型、自动数组合并、单例模式和服务提供者机制。

## 文档

📚 详细的文档请查看 [/docs](./docs) 目录：

- [项目概述](./docs/overview.md) - 项目介绍、特性说明、架构设计
- [API 参考](./docs/api/) - 完整的API文档和使用指南  
- [安装与使用](./docs/installation.md) - 安装配置、快速入门、最佳实践

## 环境要求

- **PHP版本**: >= 8.2 (推荐使用最新稳定版)
- **依赖标准**: PSR-11 Container Interface ^2.0
- **构建工具**: Composer 2.x
- **测试框架**: PHPUnit ^9.0 (仅开发环境)

## 安装

使用Composer安装：

```bash
composer require fastd/container
```

## 基础使用

### 1. 创建容器实例

```php
use FastD\Container\Container;

$container = new Container();
```

### 2. 注册服务

```php
// 注册类服务
$container->add('logger', Monolog\Logger::class);

// 注册闭包服务
$container->add('database', function() {
    return new PDO('sqlite::memory:');
});

// 注册配置数组
$container->add('config', [
    'app_name' => 'My Application',
    'debug' => true
]);
```

### 3. 获取服务实例

```php
// 获取服务实例 (PSR-11 标准方法)
$logger = $container->get('logger');
$config = $container->get('config');

// 检查服务是否存在
if ($container->has('logger')) {
    $logger = $container->get('logger');
}
```

### 4. 数组访问支持

```php
// 使用数组语法操作容器
$container['cache'] = RedisCache::class;

if (isset($container['cache'])) {
    $cache = $container['cache'];
}

unset($container['cache']);
```

更多使用示例请参考 [完整文档](./docs/installation.md)。

## 测试

运行测试套件：

```bash
vendor/bin/phpunit
```

## 贡献

欢迎任何形式的贡献！您可以通过以下方式参与项目：

- 🐛 [报告问题](https://github.com/JanHuang/container/issues)
- 💡 提交功能建议
- 🔧 贡献代码和文档
- ⭐ Star项目支持

请确保在提交Pull Request前：

1. 编写相应的测试用例
2. 确保所有测试通过
3. 遵循项目的代码风格
4. 更新相关文档

## License MIT
