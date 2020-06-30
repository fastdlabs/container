<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/janhuang
 * @link      https://www.fastdlabs.com
 */

namespace FastD\Container;


use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;

/**
 * Class Reflection
 *
 * @package FastD\Container
 */
class Reflection
{
    /**
     * @param $obj
     * @param string $method
     * @return array
     * @throws ReflectionException
     */
    public static function detectionObjectArgs($obj, string $method): array
    {
        $reflection = new ReflectionMethod($obj, $method);
        return static::detectionArgs($reflection);
    }

    /**
     * @param Closure $closure
     * @return array
     * @throws ReflectionException
     */
    public static function detectionClosureArgs(Closure $closure): array
    {
        $reflection = new ReflectionFunction($closure);
        return static::detectionArgs($reflection);
    }

    /**
     * @param ReflectionFunctionAbstract $reflectionFunctionAbstract
     * @return array
     */
    public static function detectionArgs(ReflectionFunctionAbstract $reflectionFunctionAbstract): array
    {
        if (0 >= $reflectionFunctionAbstract->getNumberOfParameters()) {
            unset($reflectionFunctionAbstract);
            return [];
        }

        $args = [];
        foreach ($reflectionFunctionAbstract->getParameters() as $index => $parameter) {;
            if (($class = $parameter->getClass()) instanceof ReflectionClass) {
                $args[$index] = $class->getName();
            }
        }
        unset($reflectionFunctionAbstract);
        return $args;
    }
}
