<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\DependencyInjection\CompilerPass;

use Darkilliant\ProcessBundle\DependencyInjection\CompilerPass\StepIteratorCompilerrPass;
use Darkilliant\ProcessBundle\Step\DebugStep;
use Darkilliant\ProcessBundle\Step\IterateArrayStep;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class StepIteratorCompilerPassTest extends TestCase
{
    /** @var StepIteratorCompilerrPass */
    private $compilerPass;

    public function setUp()
    {
        $this->compilerPass = new StepIteratorCompilerrPass();
    }

    public function testProcess()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with('darkilliant_process')
            ->willReturn([
                'process' => [
                    'import_csv' => [
                        'steps' => [
                            ['service' => IterateArrayStep::class],
                            ['service' => DebugStep::class],
                        ]
                    ]
                ]
            ]);

        $container
            ->expects($this->at(1))
            ->method('findDefinition')
            ->willReturn(new Definition(IterateArrayStep::class));
        $container
            ->expects($this->at(2))
            ->method('findDefinition')
            ->willReturn(new Definition(DebugStep::class));

        $container
            ->expects($this->once())
            ->method('setParameter')
            ->with('darkilliant_process', [
                'process' => [
                    'import_csv' => [
                        'steps' => [
                            [
                                'service' => IterateArrayStep::class,
                                'children' => [
                                    [
                                        'service' => DebugStep::class,
                                    ],
                                ]
                            ],
                        ],
                    ],
                ],
            ]);

        $this->compilerPass->process($container);
    }
}