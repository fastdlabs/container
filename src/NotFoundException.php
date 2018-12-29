<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2018
 *
 * @link      https://www.github.com/fastdlabs
 * @link      https://www.fastdlabs.com/
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