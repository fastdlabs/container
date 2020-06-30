<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2020
 *
 * @link      https://www.github.com/fastdlabs
 * @link      https://www.fastdlabs.com/
 */

namespace FastD\Container;


/**
 * Interface InjectionInterface
 *
 * @package FastD\Container
 */
interface InjectionInterface
{
    /**
     * @param array $arguments
     * @return object
     */
    public function make(array $arguments = []): object ;

    /**
     * @param $instance
     * @return InjectionInterface
     */
    public function injectOn(string $instance): InjectionInterface;

    /**
     * @param string $method
     * @param bool $isStatic
     * @return InjectionInterface
     */
    public function withMethod(string $method, bool $isStatic = false): InjectionInterface;

    /**
     * @param array $arguments
     * @return InjectionInterface
     */
    public function withArguments(array $arguments): InjectionInterface;
}
