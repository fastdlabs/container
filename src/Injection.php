<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/fastdlabs
 * @link      https://www.fastdlabs.com/
 */

namespace FastD\Container;


use Psr\Container\ContainerInterface;
use ReflectionClass;

/**
 * Class Injection
 *
 * @package FastD\Container
 */
class Injection implements InjectionInterface
{
    protected ContainerInterface $container;

    /**
     * @var string
     */
    protected string $object;

    /**
     * @var string
     */
    protected string $method;

    /**
     * @var bool
     */
    protected bool $isStatic = false;

    /**
     * @var array
     */
    protected array $arguments = [];

    /**
     * Injection constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $service
     * @return Injection
     */
    public function injectOn(string $service): InjectionInterface
    {
        $this->object = $service;

        $this->withConstruct();

        return $this;
    }

    /**
     * @return Injection
     */
    public function withConstruct(): InjectionInterface
    {
        return $this->withMethod('__construct');
    }

    /**
     * @param string $name
     * @param bool $isStatic
     * @return $this
     */
    public function withMethod(string $name, bool $isStatic = false): InjectionInterface
    {
        $this->method = $name;

        $this->isStatic = $isStatic;

        return $this;
    }

    /**
     * @param array $arguments
     * @return $this
     */
    public function withArguments(array $arguments): InjectionInterface
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @param array $arguments
     * @return object
     * @throws \ReflectionException
     */
    public function newInstance(array $arguments = []): object
    {
        return (new ReflectionClass($this->object))->newInstanceArgs($arguments);
    }

    /**
     * @param array $arguments
     * @return object
     * @throws \ReflectionException
     */
    public function make(array $arguments = []): object
    {
        if (empty($this->arguments)) {
            if (is_callable($this->object)) {
                $injections = Reflection::detectionClosureArgs($this->object);
            } else {
                $injections = Reflection::detectionObjectArgs($this->object, $this->method);
            }

            foreach ($injections as $injection) {
                $this->arguments[] = $this->container->get($injection);
            }
        }

        $arguments = array_merge($this->arguments, $arguments);

        if (is_callable($this->object)) {
            return call_user_func_array($this->object, $arguments);
        }

        if ($this->isStatic) {
            return call_user_func_array($this->object . '::' . $this->method, $arguments);
        }

        if ('__construct' === $this->method) {
            return $this->newInstance($arguments);
        }

        $obj = $this->object;

        if (!is_object($obj)) {
            $obj = new $obj;
        }

        if (empty($this->method)) {
            return $obj;
        }

        return call_user_func_array([$obj, $this->method], $arguments);
    }
}
