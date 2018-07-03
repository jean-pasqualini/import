<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\ProcessNotitfier;

use Darkilliant\ProcessBundle\ProcessNotifier\BreakerProcessNotifier;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\StepInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PhpUnit\ClockMock;

class BreakerProcessNotifierTest extends TestCase
{
    /** @var BreakerProcessNotifier */
    private $notifier;

    protected function setUp()
    {
        $this->notifier = new BreakerProcessNotifier();
    }


    public static function setUpBeforeClass()
    {
        ClockMock::register(BreakerProcessNotifier::class);
        ClockMock::withClockMock(true);
    }

    public function testOnStartIterableProcess()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'breaker' => true,
            'breaker_max_time' => 60,
            'breaker_max_iteration' => 10,
            'breaker_sleep_between' => 1,
        ]);

        $logger
            ->expects($this->once())
            ->method('log')
            ->with('info', 'breaker', ['max_time' => 60, 'max_iteration' => 10, 'sleep_between' => 1]);

        $this->notifier->onStartIterableProcess($state, $this->createMock(StepInterface::class));
    }

    public function testOnStartIterableProcessWhenBreakerIsDisabled()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions($options = [
            'breaker' => false,
        ]);

        $this->assertNull(
            $this->notifier->onStartIterableProcess($state, $this->createMock(StepInterface::class))
        );
    }

    public function provideValid()
    {
        // Time
        yield 'time #1' => [[
            'options' => ['breaker' => true, 'breaker_max_time' => 60, 'breaker_max_iteration' => null, 'breaker_sleep_between' => 1],
            'sleep' => 1,
            'result' => ProcessState::RESULT_OK,
        ]];
        yield 'time #2' => [[
            'options' => ['breaker' => true, 'breaker_max_time' => 60, 'breaker_max_iteration' => null, 'breaker_sleep_between' => 1],
            'sleep' => 61,
            'result' => ProcessState::RESULT_BREAK,
        ]];

        // Iteration
        yield 'iteration #1' => [[
            'options' => ['breaker' => true, 'breaker_max_time' => null, 'breaker_max_iteration' => 2, 'breaker_sleep_between' => 1],
            'sleep' => 0,
            'result' => ProcessState::RESULT_OK,
        ]];
        yield 'iteration #2' => [[
            'options' => ['breaker' => true, 'breaker_max_time' => null, 'breaker_max_iteration' => 1, 'breaker_sleep_between' => 1],
            'sleep' => 0,
            'result' => ProcessState::RESULT_BREAK,
        ]];

        // Time before iteration when valid before
        yield 'time before iteration #1' => [[
            'options' => ['breaker' => true, 'breaker_max_time' => 0, 'breaker_max_iteration' => 2, 'breaker_sleep_between' => 1],
            'sleep' => 0,
            'result' => ProcessState::RESULT_BREAK,
        ]];
    }

    /**
     * @dataProvider provideValid
     */
    public function testOnUpdateIterableProcess($params)
    {
        list ($options, $sleep, $isValid) = array_values($params);

        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions($options);

        $step = $this->createMock(StepInterface::class);

        $state->markSuccess();

        $this->notifier->onStartIterableProcess($state, $step);
        ClockMock::sleep($sleep);
        $this->notifier->onUpdateIterableProcess($state, $step);

        $this->assertEquals($isValid, $state->getResult());
    }

    public function testOnUpdateIterableProcessWhenBreakerIsDisabled()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );

        $this->assertNull(
            $this->notifier->onUpdateIterableProcess($state, $this->createMock(StepInterface::class))
        );
    }

    public function testOnEndProcess()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );

        $this->assertNull($this->notifier->onEndProcess($state, $this->createMock(StepInterface::class)));
    }

    public function testOnExecutedProcess()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );

        $this->assertNull($this->notifier->onExecutedProcess($state, $this->createMock(StepInterface::class)));
    }

    public function testOnStartProcess()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );

        $this->assertNull($this->notifier->onStartProcess($state, $this->createMock(StepInterface::class)));
    }
}