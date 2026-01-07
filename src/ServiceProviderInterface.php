<?php

declare(strict_types=1);

namespace FastD\Container;

interface ServiceProviderInterface
{
    public function register(Container $container): void;
}
