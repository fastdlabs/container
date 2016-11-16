<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Container;

use Psr\Container\ContainerInterface;

/**
 * Class Container
 *
 * @package FastD\Container
 */
class Container implements ContainerInterface
{
    /**
     * @var Injection[]
     */
    protected $services = [];

    /**
     * @var array
     */
    protected $map = [];

    /**
     * @param $name
     * @param $service
     * @return Injection
     */
    public function add($name, $service)
    {
        if (is_object($service)) {
            $this->map[get_class($service)] = $name;
        }

        $injection = new Injection($service);

        $this->services[$name] = new $injection;

        return $injection;
    }

    /**
     * @param string $name
     * @return bool|Injection
     */
    public function get($name)
    {
        $name = $this->findService($name);

        if (false === $name || !isset($this->services[$name])) {

        }

        return isset($this->services[$name]) ? $this->services[$name] : false;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        $name = $this->findService($name);

        return isset($this->services[$name]) ? true : false;
    }

    /**
     * @param $name
     * @return string
     */
    public function findService($name)
    {
        return isset($this->map[$name]) ? $this->map[$name] : $name;
    }
}