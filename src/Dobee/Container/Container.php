<?php
/**
 * Created by PhpStorm.
 * User: JanHuang
 * Date: 2015/3/11
 * Time: 1:21
 * Email: bboyjanhuang@gmail.com
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 */

namespace Dobee\Container;

/**
 * Class Container
 *
 * @package Dobee\Container
 */
class Container implements ContainerInterface
{
    /**
     * @var Objective[]
     */
    private $container = array();

    /**
     * @param array $objectives
     */
    public function __construct(array $objectives = array())
    {
        foreach ($objectives as $alias => $class) {
            if (is_numeric($alias)) {
                $alias = $this->getAlias($class);
            }
            $this->set($alias, $this->createObjective($class));
        }
    }

    /**
     * @param string|object
     * @return Objective
     * @throw \InvalidArgumentException
     */
    public function get($name)
    {
        if (!isset($this->container[$name])) {
            $this->set($this->getAlias($name), $this->createObjective($name));
        }
        
        return $this->container[$name];
    }

    /**
     * @param       $name
     * @param array $parameters
     * @return mixed
     */
    public function getInstance($name, array $parameters = array())
    {
        return $this->get($name)->getInstance($parameters);
    }

    /**
     * @param $name
     * @return string
     */
    public function getAlias($name)
    {
        if (false !== ($pos = strrpos($name, '\\'))) {
            $alias = substr($name, $pos);
        } else {
            $alias = $name;
        }

        return $alias;
    }

    /**
     * @param           $name
     * @param Objective $objective
     * @return $this
     */
    public function set($name, Objective $objective)
    {
        $this->container[$name] = $objective;

        return $this;
    }

    /**
     * @return Objective
     */
    public function createObjective($name)
    {
        if (!class_exists($name)) {
            throw new \InvalidArgumentException(sprintf('Container "%s" is undefined.', $name));
        }

        $objective = Objective::createObjective($name);

        $objective->setContainer($this);

        $objective->setAlias($this->getAlias($name));

        return $objective;
    }
}