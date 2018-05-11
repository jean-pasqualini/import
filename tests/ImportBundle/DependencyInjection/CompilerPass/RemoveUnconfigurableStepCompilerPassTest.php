<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\DependencyInjection\CompilerPass;

use Darkilliant\ImportBundle\DependencyInjection\CompilerPass\RemoveUnconfigurableStepCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveUnconfigurableStepCompilerPassTest extends TestCase
{
    /** @var RemoveUnconfigurableStepCompilerPass */
    private $compiler;

    public function setUp()
    {
        $this->compiler = new RemoveUnconfigurableStepCompilerPass();
    }

    public function provideExtension()
    {
        return [
            ['doctrine']
        ];
    }

    /**
     * @throws \ReflectionException
     * @dataProvider provideExtension
     */
    public function testRemoveDefinitionWhenOneExtensionIsNotPresent($extensionName)
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('hasExtension')
            ->with($extensionName)
            ->willReturn(false);

        $container
            ->expects($this->atLeastOnce())
            ->method('removeDefinition');

        $this->compiler->process($container);
    }

    /**
     * @throws \ReflectionException
     */
    public function testNotRemoveDefinitionWhenAllExtensionPresent()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->any())
            ->method('hasExtension')
            ->with()
            ->willReturn(true);

        $container
            ->expects($this->never())
            ->method('removeDefinition');

        $this->compiler->process($container);
    }
}