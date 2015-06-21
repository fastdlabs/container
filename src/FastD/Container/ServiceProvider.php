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

namespace FastD\Container;

/**
 * Class ServiceProvider
 *
 * @package FastD\Container
 */
class ServiceProvider implements ProviderInterface
{
    /**
     * @var array
     */
    private $services = array();

    /**
     * @var array
     */
    private $alias = array();

    /**
     * @param array $services
     */
    public function __construct(array $services = array())
    {
        foreach ($services as $name => $service) {
            $this->setService($name, $service);
        }

        ServiceGenerator::setProvider($this);
    }

    /**
     * @param $name
     * @param $service
     * @return $this
     */
    public function setService($name, $service)
    {
        $serviceName = is_object($service) ? get_class($service) : $service;

        $serviceName = (false !== ($pos = strpos($serviceName, '::'))) ? substr($serviceName, 0, $pos) : $serviceName;

        $this->services[$serviceName] = $service;

        $this->alias[$name] = $serviceName;

        unset($name, $service, $serviceName);

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
     * @param $name
     * @return bool|mixed
     */
    public function getAlias($name)
    {
        if (isset($this->alias[$name])) {
            return $name;
        }

        if (!$this->getServiceName($name)) {
            return false;
        }

        return array_search($name, $this->alias);
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

        return false;
    }

    /**
     * @param       $name
     * @param array $arguments
     * @param bool  $flag
     * @return bool
     */
    public function getService($name, array $arguments = array(), $flag = false)
    {
        if (!($service = $this->getServiceName($name))) {
            throw new \LogicException(sprintf('Service "%s" is not exists.', $name));
        }

        if (!is_object($this->services[$service]) || $flag) {
            $this->services[$service] = $this->newInstance($this->services[$service], $arguments);
        }

        return $this->services[$service];
    }

    /**
     * @param       $service
     * @param array $arguments
     * @return mixed|object
     */
    public function newInstance($service, array $arguments = array())
    {
        return ServiceGenerator::createService($service, $arguments);
    }

    /**
     * @param       $service
     * @param       $method
     * @param array $arguments
     * @return mixed
     */
    public function callServiceMethod($service, $method, array $arguments = array())
    {
        return ServiceGenerator::callServiceCallback($service, $method, $arguments);
    }
}