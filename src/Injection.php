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
class Injection implements InjectionInterface
{
    use ContainerAware;

    /**
     * @var mixed
     */
    protected $object;

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
            $this->withConstruct();
        }
    }

    /**
     * @param $service
     * @return $this
     */
    public function injectOn($service)
    {
        $this->object = $service;

        $this->arguments = [];
        $this->isStatic = false;
        $this->method = null;

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
     * @param string $name
     * @param bool $isStatic
     * @return $this
     */
    public function withMethod($name, $isStatic = false)
    {
        $this->method = $name;

        $this->isStatic = $isStatic;

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
        return (new ReflectionClass($this->object))->newInstanceArgs($arguments);
    }

    /**
     * @param array $arguments
     * @return mixed
     */
    public function make(array $arguments = [])
    {
        if (empty($this->arguments)) {
            if (is_callable($this->object)) {
                $injections = Depend::detectionClosureArgs($this->object);
            } else {
                $injections = Depend::detectionObjectArgs($this->object, $this->method);
            }

            foreach ($injections as $injection) {
                $this->arguments[] = $this->container->get($injection);
            }
        }

        $arguments = array_merge($this->arguments, $arguments);

        if (is_callable($this->object)) {
            return call_user_func_array($this->object, $arguments);
        }

        if ($this->isStatic) {
            return call_user_func_array($this->object . '::' . $this->method, $arguments);
        }

        if ('__construct' === $this->method) {
            return $this->getInstance($arguments);
        }

        $obj = $this->object;

        if (!is_object($obj)) {
            $obj = new $obj;
        }

        var_export($this->method);
        if (empty($this->method)) {
            return $obj;
        }

        return call_user_func_array([$obj, $this->method], $arguments);
    }
}