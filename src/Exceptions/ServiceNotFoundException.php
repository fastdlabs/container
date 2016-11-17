<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Container\Exceptions;

use Psr\Container\Exception\NotFoundException;
use RuntimeException;

class ServiceNotFoundException extends RuntimeException  implements NotFoundException
{

}