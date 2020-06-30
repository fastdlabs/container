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
 * Interface ServiceProviderInterface
 *
 * @package FastD\Container
 */
interface ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void;
}
