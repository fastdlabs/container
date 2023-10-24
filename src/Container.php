<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/fastdlabs
 * @link      https://www.fastdlabs.com/
 */

namespace FastD\Container;


use Closure;
use Iterator;
use Psr\Container\ContainerInterface;

/**
 * Class Container
 *
 * @package FastD\Container
 */
class Container implements ContainerInterface, Iterator
{
    /**
     * @var array
     */
    protected array $services = [];

    /**
     * @var array
     */
    protected array $map = [];

    /**
     * 实例数组
     *
     * @var array
     */
    protected array $instances = [];

    /**
     * @param $id
     * @param $service
     * @return Container
     */
    public function set(string $id, $service): Container
    {
        if (!($service instanceof Closure)) {
            if (is_object($service)) {
                $this->map[get_class($service)] = $id;
                $this->instances[$id] = $service;
            } elseif (is_string($service)) {
                $this->map[$service] = $id;
            }
        }

        $this->services[$id] = $service;

        return $this;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        if (isset($this->map[$id])) {
            $id = $this->map[$id];
        }

        return isset($this->services[$id]);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id)
    {
        $name = $this->map[$id] ?? $id;

        if (!isset($this->services[$name])) {
            throw new NotFoundException($name);
        }

        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        $service  = $this->services[$name];

        if (is_string($service)) {
            $service = new $service;
        }

        $this->instances[$name] = $service;

        return $service;
    }

    public function remove(string $id): void
    {
        $this->offsetUnset($id);
    }

    /**
     * @param ServiceProviderInterface $registrar
     * @return void
     */
    public function register(ServiceProviderInterface $registrar): void
    {
        $registrar->register($this);
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
    public function offsetExists($offset): bool
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
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
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
    public function offsetUnset($offset): void
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
    public function next(): void
    {
        next($this->services);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key(): string
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
    public function valid(): bool
    {
        return isset($this->services[$this->key()]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        reset($this->services);
    }
}
