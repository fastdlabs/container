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

use FastD\Container\Provider\Args\Extractor;

/**
 * Class ServiceGenerator
 *
 * @package FastD\Container\Provider
 */
class ServiceGenerator
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
     * @var string|null
     */
    protected $constructor;

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
        $this->class = $class;

        if (is_object($class)) {
            $name = get_class($class);
            $this->setName($name);
            return $this;
        }

        if (strpos($class, '::')) {
            list($name, $constructor) = explode('::', $class);
            $this->setConstructor($constructor);
            $this->setName($name);
            $this->class = $name;
        } else {
            $this->setName($class);
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

    public function newInstance(array $arguments = [])
    {
        if (null === $this->getConstructor()) {
            return call_user_func_array([$this->getClass(), $this->getConstructor()], $arguments);
        }

        $class = $this->getClass();
        $constructor = $this->getConstructor();
        return call_user_func_array("{$class}::{$constructor}", $arguments);
    }

    public function __clone()
    {
        $this->name = null;
        $this->class = null;
        $this->constructor = null;
        return $this;
    }
}