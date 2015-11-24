<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/3/11
 * Time: 下午2:38
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

namespace FastD\Container\Tests;

use FastD\Container\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetServiceName()
    {
        $container = new Container();

        $container->set('demo', 'FastD\\Container\\Tests\Libs\\TestService');
        $container->set('demo2', 'FastD\\Container\\Tests\Libs\\TestService::single');

        $container->get('demo');
        print_r($container);
//        $container->get('demo');
    }
}