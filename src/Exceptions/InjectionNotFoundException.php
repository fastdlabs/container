<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Container\Exceptions;

class InjectionNotFoundException extends ContainerException
{
    public function __construct($service)
    {
        parent::__construct(sprintf('Injection service "%s" not found', $service));
    }
}