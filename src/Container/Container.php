<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/26
 * Time: 下午1:06
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * sf: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 */

namespace Dobee\Container;

use Dobee\Container\Dependency\Ioc;

class Container
{
    private $bundles;

    public function addBundles($name, $bundle)
    {
        if (!is_object($bundle)) {
            throw new ContainerException(sprintf('%s\' is not a object.'));
        }

        $this->bundles[$name] = new Ioc($bundle);

        return $this;
    }

    public function getBundles($name = null)
    {
        if (null === $name) {
            return $this->bundles;
        }

        if (!isset($this->bundles[$name])) {
            throw new ContainerException(sprintf("%s' is not register into the container."));
        }

        return $this->bundles[$name];
    }
}