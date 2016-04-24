<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/4/9
 * Time: 下午4:06
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Container;

/**
 * Class Container
 *
 * @package FastD\Container
 */
class Container implements ContainerInterface
{
    /**
     * @var Service[]
     */
    protected $services = [];

    protected $alias = [];

    protected $serviceProperty;

    /**
     * @param array $services
     */
    public function __construct(array $services = [])
    {
        $this->serviceProperty = new Service(null);

        foreach ($services as $name => $service) {
            $this->set($name, $service);
        }
    }

    /**
     * @param $name
     * @param $class
     * @return $this
     */
    public function set($name, $class)
    {
        $this->services[$class] = $class;
        $this->alias[is_integer($name) ? $class : $name] = $class;

        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->services[$name]) || isset($this->alias[$name]);
    }

    /**
     * @param $name
     * @return Service
     * @throws \Exception
     */
    public function get($name)
    {
        if (isset($this->alias[$name])) {
            $name = $this->alias[$name];
        }

        if (!isset($this->services[$name])) {
            throw new \Exception(sprintf('Service ["%s"] is not found.', $name));
        }

        $service = $this->services[$name];

        if (is_string($service)) {
            $property = clone $this->serviceProperty;

            $property->setClass($service);
            $property->setName($name);
            $property->setContainer($this);

            $service = $property;

            unset($property);
        }

        return $service;
    }

    /**
     * @param       $name
     * @param array $arguments
     * @return mixed
     */
    public function instance($name, array $arguments = [])
    {
        return $this->get($name)->instance($arguments);
    }

    /**
     * @param       $name
     * @param array $arguments
     * @return mixed
     */
    public function singleton($name, array $arguments = [])
    {
        return $this->get($name)->singleton($arguments);
    }
}