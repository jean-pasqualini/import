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
use Tests\Darkilliant\ProcessBundle\Step\DebugStepTest;

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

    public function testOnStartProcess()
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

        $this->processNotifier->onStartProcess($state, $step);
    }

    public function testOnUpdateProcess()
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

        $this->processNotifier->onStartProcess($state, $step);
        $this->processNotifier->onUpdateProcess($state, $step);
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

        $this->processNotifier->onStartProcess($state, $step);
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

        $this->processNotifier->onStartProcess($state, $step);
    }

    public function testNotStartWhenNotOptionProgressBarIsDisable()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setIterator(new \ArrayIterator());

        $step = new IterateArrayStep();

        $this->progressBar
            ->expects($this->never())
            ->method('create');

        $this->processNotifier->onStartProcess($state, $step);
    }

    public function testNotUpdateWhenProgressBarNotInitialized()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setIterator(new \ArrayIterator([]));

        $step = new IterateArrayStep();

        $this->progressBar
            ->expects($this->never())
            ->method('setProgress');

        $this->processNotifier->onUpdateProcess($state, $step);
    }

    public function testNotFinishWhenProgressBarNotInitialized()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setIterator(new \ArrayIterator([]));

        $step = new IterateArrayStep();

        $this->progressBar
            ->expects($this->never())
            ->method('finish');

        $this->processNotifier->onEndProcess($state, $step);
    }
}