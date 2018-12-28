<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2017
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace FastD\Container;


use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * Class NotFoundException
 * @package FastD\Container
 */
class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{

}