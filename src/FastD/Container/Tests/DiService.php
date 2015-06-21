<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/4/9
 * Time: 下午1:20
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Container\Tests;

class DiService 
{
    private $static;

    private $name;

    public function __construct(DiStaticService $static, $name)
    {
        $this->static = $static;

        $this->name = $name;
    }

    public function getDiStatic()
    {
        return $this->static;
    }

    public function getName()
    {
        return $this->name;
    }

    public function demoDiMethod(DiStaticService $service)
    {
        return $service;
    }
}