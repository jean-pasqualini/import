<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\WhileStep;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WhileStepTest extends TestCase
{
    /** @var WhileStep */
    private $step;

    public function setUp()
    {
        $this->step = new WhileStep();
    }

    public static function setUpBeforeClass()
    {
        ClockMock::register(WhileStep::class);
        ClockMock::withClockMock(true);
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
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions([]);

        $this->assertNull($this->step->execute($state));
    }

    public function testCount()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions([]);

        $this->assertEquals(0, $this->step->count($state));
    }

    public function testNext()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions([]);

        $this->assertEquals(null, $this->step->next($state));
    }


    public function testProgress()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions([]);

        $this->assertEquals(0, $this->step->getProgress($state));
    }


    public function testValid()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions([]);

        $this->assertEquals(true, $this->step->valid($state));
    }
}