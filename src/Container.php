<?php

declare(strict_types=1);

namespace FastD\Container;

use Closure;
use Iterator;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface, Iterator
{
    protected array $services = [];

    protected array $instances = [];

    public function register(ServiceProviderInterface $registrar): void
    {
        $registrar->register($this);
    }

    public function add(string $id, mixed $service): Container
    {
        $type = match (true) {
            $service instanceof Closure => 'closure',  // closure 是特殊的可调用对象
            is_object($service) => 'object',
            is_string($service) && class_exists($service) => 'class',  // 类名字符串
            is_callable($service) => 'callable',  // 其他可调用类型
            default => gettype($service)
        };

        if ($type == 'array' && $this->has($id)) {
            $service = array_merge($this->services[$id]['service'], $service);
        }

        $this->services[$id] = [
            'type' => $type,
            'service' => $service,
        ];

        if ($type == 'object') {
            $this->instances[$id] = $service;
        }

        return $this;
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    public function get(string $id, ...$parameters): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->services[$id])) {
            throw new NotFoundException(sprintf('Container item "%s" not found.', $id));
        }

        $service = $this->services[$id];

        $this->instances[$id] = match ($service['type']) {
            'closure', 'callable' => call_user_func_array($service['service'], $parameters),
            'object', 'class' => new $service['service'](...$parameters),
            default => $service['service']
        };

        return $this->instances[$id];
    }

    public function clear(string $id): void
    {
        if (array_key_exists($id, $this->instances)) {
            unset($this->instances[$id]);
        }

        if (array_key_exists($id, $this->services)) {
            unset($this->services[$id]);
        }
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
    public function offsetExists(mixed $offset): bool
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
    public function offsetGet(mixed $offset): mixed
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
    public function offsetSet(mixed $offset, mixed $value): void
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
    public function offsetUnset(mixed $offset): void
    {
        $this->clear($offset);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current(): mixed
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
    public function key(): mixed
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