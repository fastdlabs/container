<?php

declare(strict_types=1);

namespace FastD\Container;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{

}
