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

/**
 * Class ServiceProvider
 *
 * @package FastD\Container\Provider
 */
class ServiceProvider implements ProviderInterface
{
    /**
     * @var ServiceGenerator[]
     */
    protected $services = [];

    /**
     * @var array
     */
    protected $alias = [];

    /**
     * @var ServiceGenerator
     */
    protected $serviceGenerator;

    /**
     * @param array $services
     */
    public function __construct(array $services = array())
    {
        foreach ($services as $name => $service) {
            $this->setService($name, $service);
        }

        $this->serviceGenerator = new ServiceGenerator();
    }

    /**
     * @param $name
     * @param $service
     * @return $this
     */
    public function setService($name, $service)
    {
        $generator = clone $this->serviceGenerator;

        $generator->setClass($service);

        $this->services[$generator->getName()] = $generator;

        $this->alias[$name] = $generator->getName();

        return $this;
    }

    /**
     * @param       $name
     * @param array $arguments
     * @param bool  $flag
     * @return bool
     */
    public function getService($name, array $arguments = array(), $flag = false)
    {
        $name = $this->getServiceName($name);

        $service = $this->services[$name];

        if ($flag) {
            return $service->newInstance();
        }

        if (!($service instanceof ServiceGenerator)) {
            return $service;
        }

        $this->services[$name] = $service->newInstance();

        return $this->services[$name];
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