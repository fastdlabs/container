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

namespace FastD\Container\Tests\Libs;

class TestService 
{
    public static $ts2;

    public static function single(TestService2 $service)
    {
        self::$ts2 = $service;
        return new static();
    }

    public function testIoC(TestService2 $service2 = null)
    {
        if (null === $service2) {
            return false;
        }

        return true;
    }

    protected $name;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}