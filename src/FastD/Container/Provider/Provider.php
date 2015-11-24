<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/4/9
 * Time: 上午11:25
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Container\Provider;

use FastD\Container\Provider\Args\Extractor;

/**
 * Class ServiceProvider
 *
 * @package FastD\Container\Provider
 */
class Provider implements ProviderInterface
{
    /**
     * @var Service[]
     */
    protected $services = [];

    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @var Service
     */
    protected $service;

    /**
     * @param array $services
     */
    public function __construct(array $services = array())
    {
        foreach ($services as $name => $service) {
            $this->setService($name, $service);
        }

        $this->service = new Service();
    }

    /**
     * @param $name
     * @param $service
     * @return $this
     */
    public function setService($name, $service)
    {
        $service = clone $this->service;

        $service->setClass($service);

        $this->services[$service->getName()] = $service;

        $this->alias[$name] = $service->getName();

        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasService($name)
    {
        return isset($this->alias[$name]) || isset($this->services[$name]);
    }

    /**
     * @param       $name
     * @param array $arguments
     * @return Service
     */
    public function getService($name, array $arguments = array())
    {
        return $this->services[$this->getServiceName($name)];
    }

    /**
     * @param $name
     * @return bool
     */
    public function getServiceName($name)
    {
        if (isset($this->alias[$name])) {
            return $this->alias[$name];
        }

        if (isset($this->services[$name])) {
            return $name;
        }

        throw new \LogicException(sprintf('Service "%s" is not exists.', $name));
    }
}