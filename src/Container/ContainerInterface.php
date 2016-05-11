<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/24
 * Time: 上午11:03
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Container;

/**
 * Interface ContainerInterface
 *
 * @package FastD\Container
 */
interface ContainerInterface
{
    /**
     * @param $name
     * @return bool
     */
    public function has($name);

    /**
     * @param $name
     * @param $class
     * @return $this
     */
    public function set($name, $class);

    /**
     * @param $name
     * @return $this
     */
    public function get($name);

    /**
     * @return Service[]
     */
    public function all();

    /**
     * @param       $name
     * @param array $arguments
     * @return mixed
     */
    public function instance($name, array $arguments = []);

    /**
     * @param       $name
     * @param array $arguments
     * @return mixed
     */
    public function singleton($name, array $arguments = []);
}