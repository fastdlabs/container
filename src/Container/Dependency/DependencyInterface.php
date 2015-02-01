<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/2/1
 * Time: 下午7:36
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace Dobee\Container\Dependency;

use Dobee\Container\Handler\HandlerInterface;

interface DependencyInterface
{
    public function getMethod($method);

    public function getParameters($method, array $arguments = array());

    public function handle($action, HandlerInterface $handlerInterface);
}