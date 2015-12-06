<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/4/9
 * Time: 上午11:40
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Container\Provider;

/**
 * Class Service
 *
 * @package FastD\Container\Provider
 */
class Service
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $constructor;

    /**
     * @var Provider
     */
    protected $provider;

    /**
     * @var mixed
     */
    protected $instance;

    /**
     * @return Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param ProviderInterface $provider
     * @return $this
     */
    public function setProvider(ProviderInterface $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConstructor()
    {
        return $this->constructor;
    }

    /**
     * @param mixed $constructor
     * @return $this
     */
    public function setConstructor($constructor)
    {
        $this->constructor = $constructor;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     * @return $this
     */
    public function setClass($class)
    {
        if (is_object($class)) {
            $this->instance = $class;
            $name = get_class($class);
            $this->setName($name);
            $this->class = $name;
            return $this;
        }

        if (false !== strpos($class, '::')) {
            list($name, $constructor) = explode('::', $class);
            $this->setConstructor($constructor);
            $this->setName($name);
            $this->class = $name;
        } else {
            $this->setName($class);
            $this->class = $class;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get singleton service object.
     *
     * @param array $arguments
     * @return mixed
     */
    public function singleton(array $arguments = [])
    {
        if (null !== $this->instance) {
            return $this->instance;
        }

        $this->instance = $this->getInstance($arguments);

        return $this->instance;
    }

    /**
     * @param array $arguments
     * @return mixed
     */
    public function getInstance(array $arguments = [])
    {
        if (null === $this->getConstructor()) {
            $reflection = new \ReflectionClass($this->getClass());

            if (null !== $reflection->getConstructor()) {
                $arguments = $this->provider->extraArguments($this->getClass(), $reflection->getConstructor()->getName(), $arguments);
            }

            return $reflection->newInstanceArgs($arguments);
        }

        $arguments = $this->provider->extraArguments($this->getClass(), $this->getConstructor(), $arguments);

        return call_user_func_array("{$this->getClass()}::{$this->getConstructor()}", $arguments);
    }

    /**
     * @return $this
     */
    public function __clone()
    {
        $this->name = null;
        $this->class = null;
        $this->constructor = null;
        return $this;
    }

    /**
     * @param       $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments = [])
    {
        if (!method_exists($this->getClass(), $method)) {
            throw new \LogicException(sprintf('Method "%s" is not exists in Class "%s"', $method, $this->getClass()));
        }

        $arguments = $this->getProvider()->extraArguments($this->getClass(), $method, $arguments);

        return call_user_func_array([$this->singleton(), $method], $arguments);
    }
}