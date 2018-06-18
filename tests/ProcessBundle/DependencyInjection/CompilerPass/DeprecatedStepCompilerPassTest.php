<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\DependencyInjection\CompilerPass;

use App\Step\DeprecatedStep;
use Darkilliant\ProcessBundle\DependencyInjection\CompilerPass\DeprecatedStepCompilerPass;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DeprecatedStepCompilerPassTest extends TestCase
{
    /** @var DeprecatedStepCompilerPass */
    private $compiler;

    protected function setUp()
    {
        $this->compiler = new DeprecatedStepCompilerPass();
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
                    'demo' => [
                        'steps' => [
                            [
                                'service' => DeprecatedStep::class,
                            ]
                        ]
                    ]
                ]
            ]);

        $container
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                'darkilliant_process',
                [
                    'process' => [
                        'demo' => [
                            'deprecated' => [DeprecatedStep::class],
                            'steps' => [
                                [
                                    'service' => DeprecatedStep::class,
                                ]
                            ]
                        ]
                    ]
                ]
            );

        $this->compiler->process($container);
    }
}