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

use FastD\Container\Provider\ServiceProvider;

/**
 * Class Container
 *
 * @package FastD\Container
 */
class Container implements ContainerInterface
{
    /**
     * @var ServiceProvider
     */
    protected $provider;

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
}