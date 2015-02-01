<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/26
 * Time: 下午1:05
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * sf: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 */

namespace Dobee\Container\Dependency;

use Dobee\Container\Handler\HandlerInterface;
use Dobee\Container\Handler\HandlerAbstract;

class Ioc implements DependencyInterface
{
    private $bundle;

    private $reflection;

    /**
     * @var HandlerAbstract|HandlerInterface[]
     */
    private $event_listeners;

    public function __construct($bundle)
    {
        $this->bundle = $bundle;

        $this->reflection = new \ReflectionClass($bundle);
    }

    public function getMethod($method)
    {
        return $this->reflection->getMethod($method)->getName();
    }

    public function getParameters($method, array $arguments = array())
    {
        $parameters = $this->reflection->getMethod($method)->getParameters();

        $i = 0;
        $temp = $arguments;

        $arguments = array();

        foreach ($parameters as $index => $param) {
            if (is_object($param->getClass())) {
                if (!class_exists(($class = $param->getClass()->getName()))) {
                    throw new \Exception(sprintf("Class '%s' is undefined.", $class));
                }

                $arg = new $class();
            } else {
                $arg = $temp[$i];
                $i++;
            }

            $arguments[$index] = $arg;
        }

        return $arguments;
    }

    public function handle($action, HandlerInterface $handlerInterface)
    {
        $this->event_listeners[$action] = $handlerInterface;

        return $this;
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->bundle, $method)) {
            throw new \BadMethodCallException(sprintf("Method '%s' is undefined.", $method));
        }

        $parameters = $this->getParameters($method, $arguments);

        if (isset($this->event_listeners[$method])) {
            $listener = $this->event_listeners[$method];

            $listener->setParameters($parameters);

            $listener->before();
        }

        $result = call_user_func_array([$this->bundle, $method], $parameters);
        
        if (isset($this->event_listeners[$method])) {
            $listener->after();
        }

        return $result;
    }
}