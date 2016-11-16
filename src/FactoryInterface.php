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
 * Interface FactoryInterface
 *
 * @package FastD\Container
 */
interface FactoryInterface
{
    /**
     * @param $name
     * @param array $arguments
     * @return mixed
     */
    public function make($name, array $arguments = []);
}