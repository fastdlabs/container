<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/26
 * Time: 下午1:07
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * sf: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 */

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/Demo.php';

$container = new Dobee\Container\Container();

$container->addBundles('demo', new Demo());

$demo = $container->getBundles('demo');

$demo->handle('test', new DemoEventHandler());

echo '<pre>';

$demo->test('ab', 123, 'abcdefg');
