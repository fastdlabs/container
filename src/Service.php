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

namespace FastD\Container;

/**
 * Class Service
 *
 * @package FastD\Container
 */
class Service
{
    use ContainerAware;

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
     * @var mixed
     */
    protected $instance;

    /**
     * Service constructor.
     * @param $class
     */
    public function __construct($class)
    {
        if (null !== $class) {
            $this->setClass($class);
        }
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
            $reflection = new \ReflectionClass($class);
            if (null !== $reflection->getConstructor()) {
                $this->setConstructor($reflection->getConstructor()->getName());
            }
            unset($reflection);
        } else if (false !== strpos($class, '::')) {
            list($name, $constructor) = explode('::', $class);
            $this->setConstructor($constructor);
            $this->setName($name);
            $this->class = $name;
            unset($name, $constructor);
        } else {
            $this->setName($class);
            $this->class = $class;
            $reflection = new \ReflectionClass($class);
            if (null !== $reflection->getConstructor()) {
                $this->setConstructor($reflection->getConstructor()->getName());
            }
            unset($reflection);
        }

        unset($class);

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
     * @param array $parameters
     * @return mixed
     */
    public function singleton(array $parameters = [])
    {
        if (null === $this->instance) {
            $this->instance = $this->instance($parameters);
        }

        return $this->instance;
    }

    /**
     * @param array $parameters
     * @return mixed
     */
    public function instance(array $parameters = [])
    {
        if (null === $this->getConstructor() || '__construct' === $this->getConstructor()) {

            $reflection = new \ReflectionClass($this->getClass());

            $parameters = $this->getParameters($this->getConstructor(), $parameters);

            return $reflection->newInstanceArgs($parameters);
        }

        $parameters = $this->getParameters($this->getConstructor(), $parameters);

        return call_user_func_array("{$this->getClass()}::{$this->getConstructor()}", $parameters);
    }

    /**
     * @param       $method
     * @param array $parameters
     * @return array
     */
    public function getParameters($method, array $parameters = [])
    {
        if (empty($method)) {
            return [];
        }

        $reflection = new \ReflectionMethod($this->getClass(), $method);

        if (0 >= $reflection->getNumberOfParameters()) {
            unset($reflection);
            return $parameters;
        }

        $args = array();

        foreach ($reflection->getParameters() as $index => $parameter) {;
            if (($class = $parameter->getClass()) instanceof \ReflectionClass) {
                $name = $class->getName();
                if (!$this->getContainer()->has($name)) {
                    $this->getContainer()->set($name, $name);
                }

                $args[$index] = $this->getContainer()->singleton($name);
            }
        }
        unset($reflection);

        return array_merge($args, $parameters);
    }

    /**
     * @param       $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, array $parameters = [])
    {
        if (!method_exists($this->getClass(), $method)) {
            throw new \LogicException(sprintf('Method "%s" is not exists in Class "%s"', $method, $this->getClass()));
        }

        $parameters = $this->getParameters($method, $parameters);

        return call_user_func_array([$this->singleton(), $method], $parameters);
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
}