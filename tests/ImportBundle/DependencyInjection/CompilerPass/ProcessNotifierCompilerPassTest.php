<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\DependencyInjection\CompilerPass;

use Darkilliant\ProcessBundle\DependencyInjection\CompilerPass\ProcessNotifierCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ProcessNotifierCompilerPassTest extends TestCase
{
    /** @var ProcessNotifierCompilerPass */
    private $compiler;

    public function setUp()
    {
        $this->compiler = new ProcessNotifierCompilerPass();
    }

    public function testRegisterTransformerWithTag()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $registryDefintion = new Definition();

        $container
            ->expects($this->once())
            ->method('findDefinition')
            ->willReturn($registryDefintion);

        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->willReturn([
                'one_notifier' => []
            ]);

        $this->compiler->process($container);

        $this->assertEquals([
            ['add', [new Reference('one_notifier')]],
        ], $registryDefintion->getMethodCalls());
    }
}
