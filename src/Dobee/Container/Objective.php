<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/3/11
 * Time: 上午10:05
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace Dobee\Container;

/**
 * Class Objective
 *
 * @package Dobee\Container
 */
class Objective extends \ReflectionClass
{
    use ObjectiveTrait;

    /**
     * @var mixed
     */
    private $instance;

    /**
     * @param mixed $class
     */
    public function __construct($class)
    {
        parent::__construct($class);
    }

    /**
     * @param array $parameters
     * @return mixed
     */
    public function getInstance(array $parameters = array())
    {
        if (null !== $this->instance) {
            return $this->instance;
        }

        if (empty($this->constructor)) {
            if (null !== ($constructor = $this->getConstructor())) {
                $parameters = $this->getParameters($constructor, $parameters);
            }
        }

        $this->instance = $this->newInstanceArgs($parameters);

        return $this->instance;
    }

    /**
     * @param \ReflectionMethod $method
     * @param array             $parameters
     * @return array
     */
    public function getParameters(\ReflectionMethod $method = null, array $parameters = array())
    {
        if (null === $method || 0 >= $method->getNumberOfRequiredParameters()) {
            return $parameters;
        }

        $args = array();

        foreach ($method->getParameters() as $index => $parameter) {
            if (($class = $parameter->getClass()) instanceof \ReflectionClass) {
                $args[$index] = $this->container->getInstance($this->container->getAlias($class->getName()));
            }
        }

        return array_merge($args, $parameters);
    }

    /**
     * @param       $method
     * @param array $parameters
     * @return mixed
     */
    public function callMethod($method, array $parameters = array())
    {
        return call_user_func_array(array($this->getInstance(), $method), $this->getParameters($this->getMethod($method), $parameters));
    }

    /**
     * @param $class
     * @return Objective|static
     */
    public static function createObjective($class)
    {
        return new static($class);
    }
}