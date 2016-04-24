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

    protected $serviceProperty;

    /**
     * @param array $services
     */
    public function __construct(array $services = [])
    {
        foreach ($services as $name => $service) {
            $this->set($name, $service);
        }

        $this->serviceProperty = new Service(null);
    }

    /**
     * @param $name
     * @param $service
     * @return $this
     */
    public function set($name, $service)
    {
        $service = clone $this->serviceProperty;

        $this->services[is_integer($name) ? $service : $name] = new Service($service);

        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->services[$name]);
    }

    /**
     * @param       $name
     * @return Service
     */
    public function get($name)
    {
        return $this->has($name) ? $this->services[$name] : false;
    }

    /**
     * @param       $name
     * @param array $arguments
     * @return mixed
     */
    public function instance($name, array $arguments = [])
    {
        $service = $this->get($name);

        return new $service;
    }

    /**
     * @param       $name
     * @param array $arguments
     * @return mixed
     */
    public function singleton($name, array $arguments = [])
    {

    }
}