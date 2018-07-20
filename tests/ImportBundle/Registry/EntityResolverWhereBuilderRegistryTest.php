<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Registry;

use Darkilliant\ImportBundle\WhereBuilder\WhereBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class EntityResolverWhereBuilderRegistryTest extends TestCase
{
    /** @var ContainerInterface|MockObject */
    private $container;

    /** @var EntityResolverWhereBuilderRegistry */
    private $registry;

    /** @var WhereBuilderInterface|MockObject */
    private $whereBuilder;

    public function setUp()
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->registry = new EntityResolverWhereBuilderRegistry($this->container);
        $this->whereBuilder = $this->createMock(WhereBuilderInterface::class);
    }

    public function testResolveService()
    {
        $this->container
            ->expects($this->exactly(2))
            ->method('get')
            ->with('test')
            ->willReturn($this->whereBuilder);

        $this->assertInstanceOf(WhereBuilderInterface::class, $this->registry->resolveService('test'));
        $this->assertEquals($this->whereBuilder, $this->registry->resolveService('test'));
    }
}
