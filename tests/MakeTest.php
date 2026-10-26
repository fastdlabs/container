<?php

declare(strict_types=1);

use FastD\Container\Container;
use PHPUnit\Framework\TestCase;

final class DemoDependency
{
}

final class DemoService
{
    public function __construct(public readonly DemoDependency $dependency)
    {
    }
}

final class DemoController
{
    public function __construct(public readonly DemoService $service)
    {
    }
}


interface DemoContract
{
}

final class DemoImplementation implements DemoContract
{
}

final class DemoConsumer
{
    public function __construct(public readonly DemoContract $contract)
    {
    }
}

final class DemoCircularA
{
    public function __construct(DemoCircularB $b)
    {
    }
}

final class DemoCircularB
{
    public function __construct(DemoCircularA $a)
    {
    }
}

final class MakeTest extends TestCase
{
    public function testMakeResolvesConstructorDependencies(): void
    {
        $container = new Container();
        $dependency = new DemoDependency();
        $container->add(DemoDependency::class, $dependency);

        $controller = $container->make(DemoController::class);

        $this->assertInstanceOf(DemoController::class, $controller);
        $this->assertSame($dependency, $controller->service->dependency);
    }


    public function testInterfaceBindingIsInjected(): void
    {
        $container = new Container();
        $container->add(DemoContract::class, new DemoImplementation());

        $consumer = $container->make(DemoConsumer::class);

        $this->assertInstanceOf(DemoImplementation::class, $consumer->contract);
    }

    public function testCircularDependencyIsDetected(): void
    {
        $this->expectException(RuntimeException::class);

        (new Container())->make(DemoCircularA::class);
    }
}
