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
     * @var Extractor
     */
    protected $extractor;

    /**
     * @param array $services
     */
    public function __construct(array $services = array())
    {
        foreach ($services as $name => $service) {
            $this->setService($name, $service);
        }

        $this->service = new Service();

        $this->extractor = new Extractor($this);
    }

    /**
     * @param $name
     * @param $class
     * @return $this
     */
    public function setService($name, $class)
    {
        $service = clone $this->service;

        $service->setClass($class);
        $service->setProvider($this);

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
     * @return Service
     */
    public function getService($name)
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

    /**
     * @param       $object
     * @param       $method
     * @param array $arguments
     * @return array
     */
    public function extraArguments($object, $method, array $arguments = [])
    {
        return $this->extractor->getArguments($object, $method, $arguments);
    }
}