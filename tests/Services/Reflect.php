<?php

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      https://www.fastdlabs.com
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
