<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 16/5/9
 * Time: 下午10:49
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Container;

trait Aware
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $containerInterface
     * @return $this
     */
    public function setContainer(ContainerInterface $containerInterface)
    {
        $this->container = $containerInterface;

        return $this;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}