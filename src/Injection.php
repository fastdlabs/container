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
    protected $instance;

    /**
     * @var mixed
     */
    protected $obj;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var bool
     */
    protected $isStatic = false;

    /**
     * @var array
     */
    protected $arguments = [];

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
        if (null === $this->instance) {
            $this->instance = (new ReflectionClass($this->obj))->newInstanceArgs($arguments);
        }

        return $this->instance;
    }

    /**
     * @param array $arguments
     * @return mixed
     */
    public function make(array $arguments = [])
    {
        $arguments = array_merge($this->arguments, $arguments);

        if ($this->isStatic) {
            return call_user_func_array($this->obj . '::' . $this->method, $arguments);
        }

        if ('__construct' === $this->method) {
            return $this->getInstance($arguments);
        }

        $obj = $this->instance;

        if (!is_object($obj)) {
            $obj = new $this->obj;
        }

        call_user_func_array([$obj, $this->method], $arguments);

        return $obj;
    }
}