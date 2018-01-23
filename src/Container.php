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
use Iterator;
use Psr\Container\ContainerInterface;

/**
 * Class Container
 *
 * @package FastD\Container
 */
class Container implements ContainerInterface, ArrayAccess, Iterator
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
     * @param $name
     * @param $service
     * @return Container
     */
    public function add($name, $service)
    {
        if (!($service instanceof \Closure)) {
            if (is_object($service)) {
                $this->map[get_class($service)] = $name;
            } elseif (is_string($service)) {
                $this->map[$service] = $name;
            }
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
        $name = isset($this->map[$name]) ? $this->map[$name] :  $name;

        if (!isset($this->services[$name])) {
            throw new NotFoundException($name);
        }

        $service = $this->services[$name];

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
            $name = $this->map[$name];
        }

        return isset($this->services[$name]) ? true : false;
    }

    /**
     * @param $name
     * @param array $arguments
     * @return mixed
     * @throws NotFoundException
     */
    public function make($name, array $arguments = [])
    {
        if (!$this->has($name)) {
            throw new NotFoundException($name);
        }

        if (!isset($this->injections[$name])) {
            $service = $this->get($name);

            $this->injections[$name] = (new Injection($service))->withContainer($this);
        }

        return $this->injections[$name]->make($arguments);
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

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->services);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        next($this->services);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->services);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return isset($this->services[$this->key()]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->services);
    }
}