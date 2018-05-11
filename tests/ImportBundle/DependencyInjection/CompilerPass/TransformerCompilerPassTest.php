<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\DependencyInjection\CompilerPass;

use Darkilliant\ImportBundle\DependencyInjection\CompilerPass\TransformerCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TransformerCompilerPassTest extends TestCase
{
    /** @var TransformerCompilerPass */
    private $compiler;

    public function setUp()
    {
        $this->compiler = new TransformerCompilerPass();
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
                'one_transformer' => [
                    ['alias' => 'string']
                ]
            ]);

        $this->compiler->process($container);

        $this->assertEquals([
            ['add', ['string', new Reference('one_transformer')]],
        ], $registryDefintion->getMethodCalls());
    }
}