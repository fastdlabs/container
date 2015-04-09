<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/4/9
 * Time: 上午11:35
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace Dobee\Container\Tests;

class TestService 
{
    public static function single()
    {
        return new static();
    }
}