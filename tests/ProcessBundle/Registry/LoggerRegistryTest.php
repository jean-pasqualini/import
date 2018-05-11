<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Registry;

use Darkilliant\ProcessBundle\Logger\InMemoryLogger;
use Darkilliant\ProcessBundle\Registry\LoggerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class LoggerRegistryTest extends TestCase
{
    /** @var LoggerRegistry */
    private $registry;

    /** @var ContainerInterface|MockObject */
    private $container;

    public function setUp()
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->registry = new LoggerRegistry($this->container);
    }

    public function testResolveService()
    {
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with(InMemoryLogger::class)
            ->willReturn(new InMemoryLogger());

        $step = $this->registry->resolveService(InMemoryLogger::class);
        $this->assertEquals(new InMemoryLogger(), $step);
    }
}