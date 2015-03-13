<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/3/11
 * Time: 上午11:47
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 */

include __DIR__ . '/../vendor/autoload.php';

class A
{
    private $b;

    public function __construct(B $b, $var)
    {
        $this->b = $b;

        echo $var;
    }

    public function getB()
    {
        return $this->b;
    }
}

class B{}

class C{}

$container = new \Dobee\Container\Container(null, array('var' => 1));

echo '<pre>';
$a = $container->get('A');

//$container->getInstance('a')->getB();
print_r($a->callMethod('getB'));
