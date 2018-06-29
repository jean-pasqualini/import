<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\DependencyInjection\CompilerPass;

use Darkilliant\ProcessBundle\DependencyInjection\CompilerPass\FilterCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FilterCompilerPassTest extends TestCase
{
    /** @var FilterCompilerPass */
    private $compiler;

    public function setUp()
    {
        $this->compiler = new FilterCompilerPass();
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
                'one_filter' => [
                    ['alias' => 'regex']
                ]
            ]);

        $this->compiler->process($container);

        $this->assertEquals([
            ['add', ['regex', new Reference('one_filter')]],
        ], $registryDefintion->getMethodCalls());
    }
}