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
     * @var array
     */
    private $options = array();

    /**
     * @param array $objectives
     * @param array $options
     */
    public function __construct($objectives = array(), array $options = array())
    {
        if (!empty($objectives)) {
            foreach ($objectives as $alias => $class) {
                $constructor = '';
                if (false !== ($pos = strpos($class, ':'))) {
                    list($class, $constructor) = explode(':', $class);
                }

                $this->set($this->getAlias($class), $this->createObjective($class, $constructor));
            }
        }

        $this->options = $options;
    }

    /**
     * @param string|object
     * @return Objective
     * @throw \InvalidArgumentException
     */
    public function get($name)
    {
        $alias = $this->getAlias($name);

        if (!isset($this->container[$alias])) {
            $constructor = null;
            if (false !== ($pos = strpos($name, ':'))) {
                $constructor = substr($name, $pos + 1);
            }
            $this->set($alias, $this->createObjective($name, $constructor, $this->options));
        }
        
        return $this->container[$alias];
    }

    /**
     * @param       $name
     * @param array $parameters
     * @return mixed
     */
    public function getInstance($name, array $parameters = array())
    {
        return $this->get($name)->getInstance(array_merge($this->options, $parameters));
    }

    /**
     * @param $name
     * @return string
     */
    public function getAlias($name)
    {
        if (false !== ($pos = strrpos($name, '\\'))) {
            $alias = substr($name, $pos+1);
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
    public function createObjective($class, $constructor = null, array $parameters = array())
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Container objective "%s" is undefined.', $class));
        }

        $objective = Objective::createObjective($class, $parameters);

        $objective->setContainer($this);

        $objective->setAlias($this->getAlias($class));

        if (!empty($constructor)) {
            $objective->setConstructor($constructor);
        }

        return $objective;
    }
}