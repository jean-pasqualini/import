<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\ProcessNotitfier;

use Darkilliant\ProcessBundle\ProcessNotifier\ChainProcessNotifier;
use Darkilliant\ProcessBundle\ProcessNotifier\ProcessNotifierInterface;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\StepInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ChainProcessNotifierTest extends TestCase
{
    /** @var ChainProcessNotifier */
    private $notifier;

    /** @var ProcessNotifierInterface|MockObject */
    private $fakeNotifier;

    public function setUp()
    {
        $this->notifier = new ChainProcessNotifier();
        $this->fakeNotifier = $this->createMock(ProcessNotifierInterface::class);
        $this->notifier->add($this->fakeNotifier);
    }

    public function provideMethods()
    {
        yield ['onExecutedProcess'];
        yield ['onStartProcess'];
        yield ['onEndProcess'];
        yield ['onStartIterableProcess'];
        yield ['onUpdateIterableProcess'];
    }

    /**
     * @dataProvider provideMethods
     */
    public function testDispatchMethods($method)
    {
        $step = $this->createMock(StepInterface::class);
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );

        $this->fakeNotifier
            ->expects($this->once())
            ->method($method)
            ->with($state, $step);

        $this->notifier->{$method}($state, $step);
    }
}
