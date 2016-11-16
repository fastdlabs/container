<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Container;

class ReflectInjection extends Injection
{
    public function reflection($method)
    {

    }

    public function make(array $arguments = [])
    {
        return parent::make($arguments);
    }
}