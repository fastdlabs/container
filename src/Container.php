<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Container;

use ArrayAccess;
use FastD\Container\Exceptions\InjectionNotFoundException;
use FastD\Container\Exceptions\ServiceNotFoundException;

/**
 * Class Container
 *
 * @package FastD\Container
 */
class Container implements ContainerInterface, ArrayAccess
{
    /**
     * @var array
     */
    protected $services = [];

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

        $this->services[$name] = $service;

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

        if (!isset($this->services[$name])) {
            throw new ServiceNotFoundException($name);
        }

        $service = $this->services[$name];

        if (is_object($service)) {
            // magic invoke class
            if (method_exists($service, '__invoke')) {
                return $service;
            }

            // anonymous function
            if (is_callable($service)) {
                return $service($this);
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

        return isset($this->services[$name]) ? true : false;
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

        $this->services[$name] = $service->make($arguments);

        return $this->services[$name];
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

    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     * @return boolean true on success or false on failure.
     *                      </p>
     *                      <p>
     *                      The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if (isset($this->map[$offset])) {
            unset($this->map[$offset]);
        }

        if (isset($this->services[$offset])) {
            unset($this->services[$offset]);
        }
    }
}