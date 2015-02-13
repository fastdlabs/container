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
class Test{}

class DemoEventHandler extends \Dobee\Container\Handler\HandlerAbstract
{
    public function after()
    {
        echo 'after<br />';
    }

    public function before()
    {
        echo 'before<br />';
        print_r($this->getParameters());
        die;
    }
}

class Demo
{
    public function test(Test $test, $b, $a, $c)
    {
        print_r(func_get_args());
    }
}