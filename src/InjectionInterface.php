<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
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
     * @return mixed
     */
    public function make(array $arguments = []);

    /**
     * @param $instance
     * @return mixed
     */
    public function injectOn($instance);

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