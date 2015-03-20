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
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @param Container $container
     * @return $this
     */
    public function setContainer(Container &$container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param $alias
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @var mixed
     */
    private $instance;

    /**
     * @var string
     */
    private $constructor;

    /**
     * @var array
     */
    private $parameters = array();

    /**
     * @param $constructor
     * @return $this
     */
    public function setConstructor($constructor)
    {
        $this->constructor = $constructor;

        return $this;
    }

    /**
     * @param mixed $class
     * @param array $parameters
     */
    public function __construct($class, array $parameters = array())
    {
        parent::__construct($class);

        if (is_object($class)) {
            $this->instance = $class;
        }

        $this->parameters = $parameters;
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

        $parameters = array_merge($this->parameters, $parameters);

        if (empty($this->constructor)) {
            if (null === $this->getConstructor()) {
                $this->instance = $this->newInstance();
            } else {
                if (null !== ($constructor = $this->getConstructor())) {
                    $parameters = $this->getParameters($constructor, array_merge($this->parameters, $parameters));
                }

                $this->instance = $this->newInstanceArgs($parameters);
            }
        } else {
            $this->instance = call_user_func_array($this->getName() . '::' . $this->constructor, $this->getParameters($this->getMethod($this->constructor), $parameters));
        }

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
                $args[$index] = $this->container->getInstance($class->getName());
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
    public static function createObjective($class, array $parameters = array())
    {
        return new static($class, $parameters);
    }
}