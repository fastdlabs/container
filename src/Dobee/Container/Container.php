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

namespace Dobee\Container;

/**
 * Class Container
 *
 * @package Dobee\Container
 */
class Container
{
    /**
     * @var ServiceProvider
     */
    private $provider;

    /**
     * @param array $services
     */
    public function __construct(array $services = array())
    {
        $this->provider = new ServiceProvider($services);
    }

    /**
     * @param $name
     * @param $service
     * @return $this
     */
    public function set($name, $service)
    {
        $this->provider->setService($name, $service);

        return $this;
    }

    /**
     * @param       $name
     * @param array $arguments
     * @param bool  $flag
     * @return mixed
     */
    public function get($name, array $arguments = array(), $flag = false)
    {
        return $this->provider->getService($name, $arguments, $flag);
    }

    /**
     * @return ServiceProvider
     */
    public function getProvider()
    {
        return $this->provider;
    }
}