<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/4/9
 * Time: 上午11:40
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace Dobee\Container;

/**
 * Class ServiceGenerator
 *
 * @package Dobee\Container
 */
class ServiceGenerator
{
    /**
     * @var ProviderInterface
     */
    static $provider;

    public static function setProvider(ProviderInterface $interface)
    {
        self::$provider = $interface;
    }

    /**
     * @param       $service
     * @param array $arguments
     * @return mixed|object
     */
    public static function createService($service, array $arguments = array())
    {
        $constructor = null;

        if (false !== ($pos = strpos($service, '::'))) {
            $constructor = substr($service, $pos + 2);
            $service = substr($service, 0, $pos);
        }

        $reflection = new \ReflectionClass($service);
        
        if (null === $constructor) {
            return $reflection->newInstanceArgs(self::getArguments($reflection->getConstructor(), $arguments));
        }

        return call_user_func_array(
            $service . '::' . $constructor,
            self::getArguments($reflection->getMethod($constructor), $arguments)
        );
    }

    /**
     * @param       $service
     * @param null  $method
     * @param array $arguments
     * @return mixed
     */
    public static function callServiceCallback($service, $method = null, array $arguments = array())
    {
        $reflection = new \ReflectionClass($service);

        $callback = call_user_func_array(array($service, $method), self::getArguments($reflection->getMethod($method), $arguments));

        unset($reflection, $service, $method, $arguments);

        return $callback;
    }

    /**
     * @param \ReflectionMethod $method
     * @param array             $arguments
     * @return array
     */
    public static function getArguments(\ReflectionMethod $method = null, array $arguments = array())
    {
        if (null === $method || count($arguments) === $method->getNumberOfParameters()) {
            return $arguments;
        }

        $args = array();

        foreach ($method->getParameters() as $index => $parameter) {
            if (($class = $parameter->getClass()) instanceof \ReflectionClass) {
                $args[$index] = self::$provider->getService($class->getName());
            }
        }

        return array_merge($args, $arguments);
    }
}