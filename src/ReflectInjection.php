<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Container;

use ReflectionClass;
use ReflectionMethod;

/**
 * Class ReflectInjection
 *
 * @package FastD\Container
 */
class ReflectInjection extends Injection
{
    /**
     * @param $method
     * @return array
     */
    public function reflection($method)
    {
        $reflection = new ReflectionMethod($this->obj, $method);
        if (0 >= $reflection->getNumberOfParameters()) {
            unset($reflection);
            return [];
        }

        $args = [];
        foreach ($reflection->getParameters() as $index => $parameter) {;
            if (($class = $parameter->getClass()) instanceof ReflectionClass) {
                $name = $class->getName();
                $args[$index] = new $name;
            }
        }
        unset($reflection);
        return $args;
    }

    /**
     * @param array $arguments
     * @return mixed
     */
    public function make(array $arguments = [])
    {
        $args = $this->reflection($this->method);

        $arguments = array_merge($args, $arguments);

        return parent::make($arguments);
    }
}