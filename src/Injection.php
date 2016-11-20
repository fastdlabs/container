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

/**
 * Class Injection
 *
 * @package FastD\Container
 */
class Injection implements FactoryInterface, InjectionInterface
{
    /**
     * @var mixed
     */
    public $instance;

    /**
     * @var mixed
     */
    public $obj;

    /**
     * @var string
     */
    public $method;

    /**
     * @var bool
     */
    public $isStatic = false;

    /**
     * @var array
     */
    public $arguments = [];

    /**
     * Injection constructor.
     *
     * @param null $service
     */
    public function __construct($service = null)
    {
        if (null !== $service) {
            $this->injectOn($service);
        }
    }

    /**
     * @param $service
     * @return $this
     */
    public function injectOn($service)
    {
        $this->obj = $service;

        $this->arguments = [];
        $this->isStatic = false;
        $this->method = null;
        $this->instance = null;

        return $this;
    }

    /**
     * @return Injection
     */
    public function withConstruct()
    {
        return $this->withMethod('__construct');
    }

    /**
     * @param $name
     * @return $this
     */
    public function withMethod($name)
    {
        $this->method = $name;

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function withStatic($name)
    {
        $this->method = $name;

        $this->isStatic = true;

        return $this;
    }

    /**
     * @param array $arguments
     * @return $this
     */
    public function withArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @param array $arguments
     * @return object
     */
    public function getInstance(array $arguments = [])
    {
        return (new ReflectionClass($this->obj))->newInstanceArgs($arguments);
    }

    /**
     * @param array $arguments
     * @return mixed
     */
    public function make(array $arguments = [])
    {
        $arguments = array_merge($this->arguments, $arguments);

        if (is_callable($this->obj)) {
            return call_user_func_array($this->obj, $arguments);
        }

        if ($this->isStatic) {
            return call_user_func_array($this->obj . '::' . $this->method, $arguments);
        }

        if ('__construct' === $this->method) {
            return $this->getInstance($arguments);
        }

        $obj = $this->obj;

        if (!is_object($obj)) {
            $obj = new $obj;
        }

        if (empty($this->method)) {
            return $obj;
        }

        call_user_func_array([$obj, $this->method], $arguments);

        return $obj;
    }

    /**
     * @param array $arguments
     * @return mixed
     */
    public function singleton(array $arguments = [])
    {
        $instance = $this->instance;

        if (null === $instance) {
            $instance = $this->make($arguments);
            if (!is_callable($this->instance) && is_object($this->instance)) {
                $this->instance = $instance;
            }
        }

        return $instance;
    }
}