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

final class DemoRequestScopedService
{
}

final class ScopedContainerTest extends TestCase
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

    public function testScopedKeepsSharedObjectsAndResetsClassInstances(): void
    {
        $container = new Container();
        $shared = new DemoDependency();
        $container->add(DemoDependency::class, $shared);
        $container->add(DemoRequestScopedService::class, DemoRequestScopedService::class);

        $scopeA = $container->scoped();
        $scopeB = $container->scoped();

        $this->assertSame($shared, $scopeA->get(DemoDependency::class));
        $this->assertSame($shared, $scopeB->get(DemoDependency::class));

        $serviceA = $scopeA->get(DemoRequestScopedService::class);
        $serviceB = $scopeB->get(DemoRequestScopedService::class);

        $this->assertNotSame($serviceA, $serviceB);
        $this->assertSame($serviceA, $scopeA->get(DemoRequestScopedService::class));
    }
}
