<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/31
 * Time: 下午6:18
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace Dobee\Container\Handler;

interface HandlerInterface 
{
    public function after();

    public function before();
}