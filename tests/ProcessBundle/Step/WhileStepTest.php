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
        $state->setOptions($options = [
            'max_time' => 60,
            'max_iteration' => 10,
            'sleep_between' => 1,
        ]);

        $logger
            ->expects($this->once())
            ->method('log')
            ->with('info', 'while', $options);

        $this->step->execute($state);
    }

    public function testResultFailWhenNoMaxTimeNoMaxIteration()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'max_time' => null,
            'max_iteration' => null,
            'sleep_between' => 1,
        ]);

        $logger
            ->expects($this->once())
            ->method('log')
            ->with('error', 'please set max_time or max_iteration', []);

        $this->step->execute($state);

        $this->assertEquals(ProcessState::RESULT_KO, $state->getResult());
    }

    public function testCountWithMaxTime()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'max_time' => 60,
            'max_iteration' => null,
            'sleep_between' => 1,
        ]);

        $this->step->execute($state);
        $this->assertEquals(60, $this->step->count($state));
    }

    public function testCountWithMaxIteration()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'max_time' => null,
            'max_iteration' => 10,
            'sleep_between' => 1,
        ]);

        $this->step->execute($state);
        $this->assertEquals(10, $this->step->count($state));
    }

    public function testProgressWithMaxTime()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'max_time' => 60,
            'max_iteration' => null,
            'sleep_between' => 1,
        ]);

        $this->step->execute($state);
        ClockMock::sleep(5);
        $this->step->next($state);
        $this->assertEquals(5, $this->step->getProgress($state));
    }

    public function testProgressWithMaxIteration()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'max_time' => null,
            'max_iteration' => 10,
            'sleep_between' => 1,
        ]);

        $this->step->execute($state);
        $this->step->next($state);
        $this->assertEquals(1, $this->step->getProgress($state));
    }

    public function testGetProgressWhenNoMaxTimeNoMaxIteration()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'max_time' => null,
            'max_iteration' => null,
            'sleep_between' => 1,
        ]);

        $this->assertEquals(0, $this->step->getProgress($state));
    }

    public function provideValid()
    {
        // Time
        yield [[
            'options' => ['max_time' => 60, 'max_iteration' => null, 'sleep_between' => 1],
            'sleep' => 1,
            'valid' => true,
        ]];
        yield [[
            'options' => ['max_time' => 60, 'max_iteration' => null, 'sleep_between' => 1],
            'sleep' => 61,
            'valid' => false,
        ]];

        // Iteration
        yield [[
            'options' => ['max_time' => null, 'max_iteration' => 2, 'sleep_between' => 1],
            'sleep' => 0,
            'valid' => true,
        ]];
        yield [[
            'options' => ['max_time' => null, 'max_iteration' => 1, 'sleep_between' => 1],
            'sleep' => 0,
            'valid' => false,
        ]];

        // Time before iteration when valid before
        yield [[
            'options' => ['max_time' => 0, 'max_iteration' => 2, 'sleep_between' => 1],
            'sleep' => 0,
            'valid' => false,
        ]];
    }

    /**
     * @dataProvider provideValid
     */
    public function testValid($params)
    {
        list ($options, $sleep, $isValid) = array_values($params);

        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions($options);

        $this->step->execute($state);
        ClockMock::sleep($sleep);
        $this->step->next($state);

        $this->assertEquals($isValid, $this->step->valid($state));
    }
}