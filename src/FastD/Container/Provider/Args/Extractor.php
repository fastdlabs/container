<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/11/24
 * Time: 上午10:31
 * Github: https://www.github.com/janhuang
 * Coding: https://www.coding.net/janhuang
 * SegmentFault: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 * Gmail: bboyjanhuang@gmail.com
 * WebSite: http://www.janhuang.me
 */

namespace FastD\Container\Provider\Args;

use FastD\Container\Provider\ProviderInterface;

/**
 * Class Extractor
 *
 * @package FastD\Container\Provider\Args
 */
class Extractor implements ExtraInterface
{
    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * Extractor constructor.
     *
     * @param ProviderInterface $providerInterface
     */
    public function __construct(ProviderInterface $providerInterface)
    {
        $this->provider = $providerInterface;
    }

    /**
     * @param       $object
     * @param       $method
     * @param array $arguments
     * @return array
     */
    public function getArguments($object, $method, array $arguments = [])
    {
        if (null === $method) {
            return $arguments;
        }

        $reflection = new \ReflectionMethod($object, $method);

        if (0 >= $reflection->getNumberOfParameters()) {
            return $arguments;
        }

        $args = array();

        foreach ($reflection->getParameters() as $index => $parameter) {;
            if (($class = $parameter->getClass()) instanceof \ReflectionClass) {
                $name = $class->getName();
                if (!$this->provider->hasService($name)) {
                    $this->provider->setService($name, $name);
                }

                $args[$index] = $this->provider->singleton($name);
            }
        }

        return array_merge($args, $arguments);
    }
}