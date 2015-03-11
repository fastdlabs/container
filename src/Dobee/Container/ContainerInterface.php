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
 * Interface ContainerInterface
 *
 * @package Dobee\Container
 */
interface ContainerInterface
{
    /**
     * @param $name
     * @return Objective
     */
    public function get($name);

    /**
     * @param           $name
     * @param Objective $objective
     * @return $this
     */
    public function set($name, Objective $objective);

    /**
     * @param $name
     * @return mixed
     */
    public function getInstance($name);
}