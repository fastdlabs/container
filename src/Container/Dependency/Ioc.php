<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/26
 * Time: 下午1:05
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * sf: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 */

namespace Dobee\Container\Dependency;

class Ioc 
{
    private $bundle;

    private $reflection;

    public function __construct($bundle)
    {
        $this->bundle = $bundle;

        $this->reflection = new \ReflectionClass($bundle);
    }

    public function getMethod($method)
    {
        return $this->reflection->getMethod($method)->getName();
    }

    public function getParameters($method, array $arguments = array())
    {
        $parameters = $this->reflection->getMethod($method)->getParameters();

        $i = 0;
        $temp = $arguments;

        $arguments = array();

        foreach ($parameters as $index => $param) {
            if (is_object($param->getClass())) {
                if (!class_exists(($class = $param->getClass()->getName()))) {
                    throw new \Exception(sprintf("Class '%s' is undefined.", $class));
                }

                $arg = new $class();
            } else {
                $arg = $temp[$i];
                $i++;
            }

            $arguments[$index] = $arg;
        }

        return $arguments;
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->bundle, $method)) {
            throw new \BadMethodCallException(sprintf("Method '%s' is undefined.", $method));
        }

        $parameters = $this->getParameters($method, $arguments);

        print_r($parameters);
    }

    public static function __callStatic($method, $args)
    {}
}