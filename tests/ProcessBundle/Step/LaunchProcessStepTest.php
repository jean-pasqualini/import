<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Configuration\ConfigurationProcess;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\LaunchProcessStep;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LaunchProcessStepTest extends TestCase
{
    /** @var LaunchProcessStep */
    private $step;

    public function setUp()
    {
        $this->step = new LaunchProcessStep();
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->step->configureOptionResolver($optionResolver)
        );
    }

    public function testExecuteWithIsolateState()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions(['process' => 'chocapic', 'share_state' => false, 'context' => ['filepath' => 'file.csv']]);

        $process = ConfigurationProcess::create(['steps' => []]);

        $stepRunner
            ->expects($this->once())
            ->method('buildConfigurationProcess')
            ->with('chocapic')
            ->willReturn($process);
        $stepRunner
            ->expects($this->once())
            ->method('run')
            ->with($process, ['filepath' => 'file.csv']);

        $this->step->execute($state);
    }
}