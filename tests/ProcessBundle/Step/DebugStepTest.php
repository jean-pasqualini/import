<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\DebugStep;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class DebugStepTest extends TestCase
{
    /** @var DebugStep */
    private $step;

    public function setUp()
    {
        $this->step = new DebugStep();
    }

    public function testExecute()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setData(['size' => 'xl']);

        $logger
            ->expects($this->once())
            ->method('log')
            ->with(LogLevel::INFO, 'current data', [
                'data' => ['size' => 'xl'],
            ]);

        $this->step->execute($state);
    }

    public function testDescribe()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );

        $logger
            ->expects($this->once())
            ->method('log')
            ->with(LogLevel::INFO, 'debug data', []);

        $this->step->describe($state);
    }

    public function testCount()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $this->assertNull($this->step->count($state));
    }

    public function testGetProgress()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $this->assertEquals(0, $this->step->getProgress($state));
    }

    public function testFinalize()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $this->assertNull($this->step->finalize($state));
    }
}