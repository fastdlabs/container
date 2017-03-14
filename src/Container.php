<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Container;


use ArrayIterator;
use FastD\Container\Exceptions\InjectionNotFoundException;
use FastD\Container\Exceptions\ServiceNotFoundException;

/**
 * Class Container
 *
 * @package FastD\Container
 */
class Container extends ArrayIterator implements ContainerInterface
{
    /**
     * @var array
     */
    protected $map = [];

    /**
     * @var Injection[]
     */
    protected $injections = [];

    /**
     * @var string
     */
    protected $active;

    /**
     * @param $name
     * @param $service
     * @return Container
     */
    public function add($name, $service)
    {
        $this->active = $name;

        if (!is_callable($service) && is_object($service)) {
            $this->map[get_class($service)] = $name;
        }

        $this->offsetSet($name, $service);

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if (isset($this->map[$name])) {
            $name = $this->map[$name];
        }

        if (!$this->offsetExists($name)) {
            throw new ServiceNotFoundException($name);
        }

        $service = $this->offsetGet($name);

        if (is_object($service)) {
            // magic invoke class
            if (method_exists($service, 'bindTo') && is_callable($service)) {
                return $service($this);
            }
            // anonymous function
            if (is_callable($service)) {
                return $service;
            }
        }

        return $service;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if (isset($this->map[$name])) {
            return $this->map[$name];
        }

        return $this->offsetExists($name);
    }

    /**
     * @param $name
     * @param $object
     * @return Injection
     */
    public function injectOn($name, $object)
    {
        $name = null === $name ? $this->active : $name;

        $injection = new Injection($object);

        $injection->setContainer($this);

        $this->injections[$name] = $injection;

        return $injection;
    }

    /**
     * @param $name
     * @param array $arguments
     * @return mixed
     */
    public function make($name, array $arguments = [])
    {
        if (!isset($this->injections[$name])) {
            throw new InjectionNotFoundException($name);
        }

        $service = $this->injections[$name];

        $obj = $service->make($arguments);
        $this->offsetSet($name, $obj);

        return $obj;
    }

    /**
     * @param ServiceProviderInterface $serviceProvider
     * @return Container
     */
    public function register(ServiceProviderInterface $serviceProvider)
    {
        $serviceProvider->register($this);

        return $this;
    }
}