<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\PredefinedDataStep;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PredefinedDataStepTest extends TestCase
{
    /** @var PredefinedDataStep */
    private $step;

    public function setUp()
    {
        $this->step = new PredefinedDataStep();
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->step->configureOptionResolver($optionResolver)
        );
    }

    public function testExecute()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['data' => ['size' => 'xl']]);

        $this->step->execute($state);

        $this->assertEquals(['size' => 'xl'], $state->getData());
    }

    public function testDescribe()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setData('data in pipe');

        $logger
            ->expects($this->once())
            ->method('log')
            ->with(LogLevel::INFO, 'use predefined data for debug', [
                'data' => 'data in pipe'
            ]);

        $this->step->describe($state);
    }
}