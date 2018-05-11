<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Registry;

use Darkilliant\ProcessBundle\Registry\StepRegistry;
use Darkilliant\ProcessBundle\Step\DebugStep;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class StepRegistryTest extends TestCase
{
    /** @var StepRegistry */
    private $registry;

    /** @var ContainerInterface|MockObject */
    private $container;

    public function setUp()
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->registry = new StepRegistry($this->container);
    }

    public function testResolveService()
    {
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with(DebugStep::class)
            ->willReturn(new DebugStep());

        $step = $this->registry->resolveService(DebugStep::class);
        $this->assertEquals(new DebugStep(), $step);
    }
}