<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/31
 * Time: 下午6:23
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace Dobee\Container\Handler;

abstract class HandlerAbstract implements HandlerInterface
{
    private $parameters;

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function hasParameters($name)
    {
        return isset($this->parameters[$name]);
    }

    public function getParameters($name = null)
    {
        if (null == $name) {
            return $this->parameters;
        }

        return $this->hasParameters($name) ? $this->parameters[$name] : false;
    }
}