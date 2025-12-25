<?php
declare(strict_types=1);

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
