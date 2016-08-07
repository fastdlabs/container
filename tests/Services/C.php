<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/4/25
 * Time: 下午8:40
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

class C
{
    public static $inc = 0;

    public static function instance()
    {
        self::$inc = 1;
    }
}