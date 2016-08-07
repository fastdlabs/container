<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/4/25
 * Time: 下午8:42
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

class D
{
    public static $inc;

    public static function instance($inc)
    {
        static::$inc = $inc;
    }
}