<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Container;

use Interop\Container\ContainerInterface;

/**
 * Class Container
 *
 * @package FastD\Container
 */
class Container implements ContainerInterface, FactoryInterface
{
    protected $services = [];

    /**
     * @var array
     */
    protected $map = [];

    public function add($name, $service)
    {
        if (is_object($service)) {
            $this->map[get_class($service)] = $name;
        }

        $this->services[$name] = $service;

        return $this;
    }

    public function get($name)
    {
        $name = $this->findService($name);

        return isset($this->services[$name]) ? $this->services[$name] : false;
    }

    public function has($name)
    {
        $name = $this->findService($name);

        return isset($this->services[$name]) ? true : false;
    }

    public function findService($name)
    {
        return isset($this->map[$name]) ? $this->map[$name] : $name;
    }

    public function singleton($name, array $arguments = [])
    {
        return $this->get($name)->singleton($arguments);
    }

    public function make($name, array $arguments = [])
    {
        // TODO: Implement make() method.
    }
}