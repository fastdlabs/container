<?php

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */
class Reflect
{
    protected $methodInjection;

    public function __construct(MethodInjection $injection)
    {
        $this->methodInjection = $injection;
    }

    public function now()
    {
        $this->methodInjection->now(new DateTime());

        return $this->methodInjection->date;
    }
}