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

        $service = $container->getProvider()->getServiceName('FastD\\Container\\Tests\\Libs\\TestService');

        
//        $this->assertEquals('FastD\\Container\\Tests\\Libs\\TestService', $service);
    }
}