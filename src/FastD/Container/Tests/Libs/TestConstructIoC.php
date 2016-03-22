<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/24
 * Time: 下午7:05
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Container\Tests\Libs;

class TestConstructIoC
{
    protected $service;

    public function __construct(TestService2 $service2)
    {
        $this->service = $service2;
    }

    public function getService()
    {
        return null === $this->service ? false : true;
    }
}