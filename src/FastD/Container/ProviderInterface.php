<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/4/9
 * Time: 下午3:54
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Container;

/**
 * Interface ProviderInterface
 *
 * @package FastD\Container
 */
interface ProviderInterface
{
    /**
     * @param $name
     * @param $service
     * @return $this
     */
    public function setService($name, $service);

    /**
     * @param $name
     * @return bool
     */
    public function hasService($name);

    /**
     * @param       $name
     * @param array $arguments
     * @param bool  $flag
     * @return mixed
     */
    public function getService($name, array $arguments = array(), $flag = false);
}