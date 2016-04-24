<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/4/24
 * Time: 下午11:50
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Container\Tests\Services;

class B
{
    public function __construct(A $a)
    {
        echo $a->age;
    }
}