<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\ProcessNotitfier;

use Darkilliant\ProcessBundle\Console\ProgressBar;
use Darkilliant\ProcessBundle\ProcessNotifier\ProgressBarProcessNotifier;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\DebugStep;
use Darkilliant\ProcessBundle\Step\IterateArrayStep;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;
use Darkilliant\ProcessBundle\Step\StepInterface;

class ProgressBarProcessNotifierTest extends TestCase
{
    /** @var ProgressBarProcessNotifier */
    private $processNotifier;

    /** @var ProgressBar|MockObject */
    private $progressBar;

    public function setUp()
    {
        $this->processNotifier = new ProgressBarProcessNotifier(
            $this->progressBar = $this->createMock(ProgressBar::class)
        );
    }

    public function testOnCommand()
    {
        $event = new ConsoleEvent(null, new ArgvInput(), new NullOutput());

        $this->assertNull($this->processNotifier->onCommand($event));
    }

    public function testOnStartIterateProcess()
    {
        $iterator = new \ArrayIterator(['color' => 'red']);
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['progress_bar' => true]);
        $state->setIterator($iterator);

        $step = new IterateArrayStep();

        $this->progressBar
            ->expects($this->once())
            ->method('create')
            ->with(1, IterateArrayStep::class);

        $this->processNotifier->onStartIterableProcess($state, $step);
    }

    public function testOnStartIterableProcessWhenNoItem()
    {
        $iterator = new \ArrayIterator([]);
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['progress_bar' => true]);
        $state->setIterator($iterator);

        $step = new IterateArrayStep();

        $this->progressBar
            ->expects($this->never())
            ->method('create');

        $this->processNotifier->onStartIterableProcess($state, $step);
    }

    public function testOnExecutedProcessWhenNoItem()
    {
        $iterator = new \ArrayIterator([]);
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );

        $this->assertNull($this->processNotifier->onExecutedProcess($state, $this->createMock(DebugStep::class)));
    }

    public function testOnUpdateIterateProcess()
    {
        $iterator = new \ArrayIterator([1 => ['color' => 'red']]);
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['progress_bar' => true]);
        $state->setIterator($iterator);

        $step = new IterateArrayStep();

        $this->progressBar
            ->expects($this->once())
            ->method('setProgress')
            ->with(1);

        $this->processNotifier->onStartIterableProcess($state, $step);
        $this->processNotifier->onUpdateIterableProcess($state, $step);
    }

    public function testOnEndProcess()
    {
        $iterator = new \ArrayIterator(['color' => 'red']);
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['progress_bar' => true]);
        $state->setIterator($iterator);

        $step = new IterateArrayStep();

        $this->progressBar
            ->expects($this->once())
            ->method('finish');

        $this->processNotifier->onStartIterableProcess($state, $step);
        $this->processNotifier->onEndProcess($state, $step);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertInternalType('array', ProgressBarProcessNotifier::getSubscribedEvents());
    }

    public function testNotStartWhenNotStepIterable()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $step = new DebugStep();

        $this->progressBar
            ->expects($this->never())
            ->method('create');

        $this->processNotifier->onStartIterableProcess($state, $step);
    }

    public function testNotStartWhenNotOptionProgressBarIsDisable()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['progress_bar' => false]);
        $state->setIterator(new \ArrayIterator());

        $step = new IterateArrayStep();

        $this->progressBar
            ->expects($this->never())
            ->method('create');

        $this->processNotifier->onStartIterableProcess($state, $step);
    }

    public function testNotUpdateWhenProgressBarNotInitialized()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setIterator(new \ArrayIterator([]));
        $state->setOptions(['progress_bar' => false]);

        $step = new IterateArrayStep();

        $this->progressBar
            ->expects($this->never())
            ->method('setProgress');

        $this->processNotifier->onUpdateIterableProcess($state, $step);
    }

    public function testNotFinishWhenProgressBarNotInitialized()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setIterator(new \ArrayIterator([]));
        $state->setOptions(['progress_bar' => false]);

        $step = new IterateArrayStep();

        $this->progressBar
            ->expects($this->never())
            ->method('finish');

        $this->processNotifier->onEndProcess($state, $step);
    }

    public function testNotProcessWhenNotIterableStep()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );

        $this->assertNull($this->processNotifier->onStartIterableProcess($state, new DebugStep()));
        $this->assertNull($this->processNotifier->onUpdateIterableProcess($state, new DebugStep()));
        $this->assertNull($this->processNotifier->onEndProcess($state, new DebugStep()));
    }

    public function testOnStartProcess()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );

        $this->assertNull($this->processNotifier->onStartProcess($state, new DebugStep()));
    }

    public function testOnSuccessLoop()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );

        $this->assertNull($this->processNotifier->onSuccessLoop($state, $this->createMock(StepInterface::class)));
    }

    public function testOnFailedLoop()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );

        $this->assertNull($this->processNotifier->onFailedLoop($state, $this->createMock(StepInterface::class)));
    }
}
