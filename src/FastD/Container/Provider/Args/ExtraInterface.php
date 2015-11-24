<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/24
 * Time: 下午4:14
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Container\Provider\Args;

interface ExtraInterface
{
    public function getArguments($object, $method, array $arguments = []);
}