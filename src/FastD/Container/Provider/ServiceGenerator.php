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

namespace FastD\Container\Provider;

/**
 * Class ServiceGenerator
 *
 * @package FastD\Container\Provider
 */
class ServiceGenerator
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string|null
     */
    protected $constructor;

    /**
     * @return mixed
     */
    public function getConstructor()
    {
        return $this->constructor;
    }

    /**
     * @param mixed $constructor
     * @return $this
     */
    public function setConstructor($constructor)
    {
        $this->constructor = $constructor;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        if (is_object($class)) {
            $name = get_class($class);
            $this->setName($name);
            return $this;
        }

        if (strpos($class, '::')) {
            list($name, $constructor) = explode('::', $class);
            $this->setConstructor($constructor);
            $this->setName($name);
            $this->class = $name;
        } else {
            $this->setName($class);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function newInstance()
    {
        if (null === $this->getConstructor()) {
            return new $this->class;
        }

        $class = $this->getClass();
        $constructor = $this->getConstructor();
        return $class::$constructor;
    }

    public function __clone()
    {
        $this->name = null;
        $this->class = null;
        $this->constructor = null;
        return $this;
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