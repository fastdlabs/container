<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/4/23
 * Time: 下午11:15
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

include __DIR__ . '/../vendor/autoload.php';

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
