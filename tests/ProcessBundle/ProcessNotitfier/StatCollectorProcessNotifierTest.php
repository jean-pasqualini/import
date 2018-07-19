<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\ProcessNotitfier;

use Darkilliant\ProcessBundle\ProcessNotifier\StatsCollectorProcessNotifier;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\StepInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PhpUnit\ClockMock;

class StatCollectorProcessNotifierTest extends TestCase
{
    /** @var StatsCollectorProcessNotifier */
    private $notifier;

    public function setUp()
    {
        $this->notifier = new StatsCollectorProcessNotifier();
    }

    public static function setUpBeforeClass()
    {
        ClockMock::register(StatsCollectorProcessNotifier::class);
        ClockMock::withClockMock(true);
    }

    public function testSetEnabled()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $step = $this->createMock(StepInterface::class);

        $this->notifier->setEnabled(false);

        $this->notifier->onStartProcess($state, $step);
        $this->notifier->onExecutedProcess($state, $step);
        $this->notifier->onStartIterableProcess($state, $step);
        $this->notifier->onUpdateIterableProcess($state, $step);
        $this->notifier->onEndProcess($state, $step);

        $this->assertEquals([], $this->notifier->getData());
    }

    public function testStat()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $step = $this->createMock(StepInterface::class);
        $class = get_class($step);

        $this->notifier->setEnabled(true);

        ClockMock::withClockMock(11111111);
        $this->notifier->onStartProcess($state, $step);
        ClockMock::withClockMock(11111112);
        $this->notifier->onExecutedProcess($state, $step);

        $this->notifier->onStartProcess($state, $step);
        ClockMock::withClockMock(11111114);
        $this->notifier->onExecutedProcess($state, $step);

        $this->assertEquals([
            $class => [
                'last_start' => 11111112000.0,
                'time' => [1000.0, 2000.0],
                'real_time' => [],
                'position' => 1,
                'wait' => [0.0],
                'last_finish' => 11111114000.0,
            ]
        ], $this->notifier->getData());
    }

    public function testOnSuccessLoop()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );

        $this->assertNull($this->notifier->onSuccessLoop($state, $this->createMock(StepInterface::class)));
    }

    public function testOnFailedLoop()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );

        $this->assertNull($this->notifier->onFailedLoop($state, $this->createMock(StepInterface::class)));
    }
}
