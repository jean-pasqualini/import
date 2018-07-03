<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\FilesystemEventStreamStep;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\Process;

class FilesystemEventStreamStepTest extends TestCase
{
    /** @var FilesystemEventStreamStep|MockObject */
    private $step;

    protected function setUp()
    {
        $this->step = $this->createPartialMock(FilesystemEventStreamStep::class, ['getProcess']);
    }

    public static function setUpBeforeClass()
    {
        ClockMock::register(FilesystemEventStreamStep::class);
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

    public function testCount()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'recursive' => false,
            'timeout' => 1,
            'event_name' => 'close_write',
            'folder' => '/',
        ]);

        $this->assertEquals(1, $this->step->count($state));
    }

    public function testGetProgress()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'recursive' => false,
            'timeout' => 1,
            'event_name' => 'close_write',
            'folder' => '/',
        ]);

        $this->assertEquals(1, $this->step->getProgress($state));
    }

    public function testValidWhenProcessIsRunning()
    {
        $process = $this->createMock(Process::class);
        $process
            ->expects($this->once())
            ->method('start');
        $process
            ->expects($this->once())
            ->method('isStarted')
            ->willReturn(false);
        $process
            ->expects($this->once())
            ->method('isRunning')
            ->willReturn(true);

        $this->step
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'recursive' => false,
            'timeout' => 1,
            'event_name' => 'close_write',
            'folder' => '/',
        ]);

        $this->step->execute($state);

        $this->assertEquals(true, $this->step->valid($state));
    }

    public function testExecute()
    {
        $process = $this->createMock(Process::class);
        $process
            ->expects($this->once())
            ->method('setTimeout')
            ->with(null);

        $this->step
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'recursive' => false,
            'timeout' => 1,
            'event_name' => 'close_write',
            'folder' => '/',
        ]);

        $this->step->execute($state);
    }

    public function testNext()
    {
        $process = $this->createMock(Process::class);
        $process
            ->expects($this->exactly(2))
            ->method('getIncrementalOutput')
            ->willReturnOnConsecutiveCalls('', '"/home/","A,B,C", "file"');

        $this->step
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($process);

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'recursive' => false,
            'timeout' => 1,
            'event_name' => 'close_write',
            'folder' => '/',
        ]);

        $this->step->execute($state);
        $this->step->next($state);

        $this->assertEquals(
            ['events' => ['A', 'B', 'C'], 'absolute_file' => '/home/file', 'folder' => '/home/', 'file' => 'file'],
            $state->getData()
        );
    }
}