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
 * Interface ContainerInterface
 *
 * @package FastD\Container
 */
interface ContainerInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function get($id);

    /**
     * @param $id
     * @return mixed
     */
    public function has($id);

    /**
     * @param ServiceProviderInterface $serviceProvider
     * @return mixed
     */
    public function register(ServiceProviderInterface $serviceProvider);
}