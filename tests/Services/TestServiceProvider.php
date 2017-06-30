<?php

/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2017
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */
class TestServiceProvider implements \FastD\Container\ServiceProviderInterface
{
    /**
     * @param \FastD\Container\Container $container
     * @return mixed
     */
    public function register(\FastD\Container\Container $container)
    {
        $container->add('timezone', new DateTimeZone('PRC'));
    }
}