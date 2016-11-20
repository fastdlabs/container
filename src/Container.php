<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Container;

use FastD\Container\Exceptions\ServiceNotFoundException;
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
     * @var Injection
     */
    protected $factory;

    public function __construct()
    {
        $this->factory = new Injection();
    }

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

        $injection = clone $this->factory;

        $this->services[$name] = $injection->injectOn($service);

        return $injection;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->find($name)->make();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function singleton($name)
    {
        return $this->find($name)->singleton();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if (isset($this->map[$name])) {
            return $this->map[$name];
        }

        return isset($this->services[$name]) ? $name : false;
    }

    /**
     * @param $name
     * @return Injection
     */
    public function find($name)
    {
        $name = $this->has($name);

        if (false === $name) {
            throw new ServiceNotFoundException($name);
        }

        $args = [];
        if (!is_object($this->services[$name]->instance)) {
            if (is_callable($this->services[$name]->obj)) {
                $args = DependDetection::detectionClosureArgs($this->services[$name]->obj);
            } else if (!empty($this->services[$name]->method) && empty($this->services[$name]->arguments)) {
                $args = DependDetection::detectionObjectArgs($this->services[$name]->obj, $this->services[$name]->method);
            }
        }

        $dependencyArgs = [];
        foreach ($args as $arg) {
            $dependencyArgs[] = $this->get($arg);
        }

        return empty($dependencyArgs) ? $this->services[$name] : $this->services[$name]->withArguments($dependencyArgs);
    }
}