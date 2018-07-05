<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\LoopStateMarker;

use Darkilliant\ProcessBundle\LoopStateMarker\FileLoopStateMarker;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileLoopStateMarkerTest extends TestCase
{
    /** @var FileLoopStateMarker */
    private $marker;

    /** @var Filesystem|MockObject */
    private $fs;

    protected function setUp()
    {
        $this->fs = $this->createMock(Filesystem::class);
        $this->marker = new FileLoopStateMarker($this->fs);
    }

    public function testOnStartLoop()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'track_loop_state' => true,
            'track_loop_state_remove_on_success' => false,
        ]);
        $state->setContext('file_finder_current', '/tmp/file');

        $this->fs
            ->expects($this->once())
            ->method('rename')
            ->with('/tmp/file', '/tmp/_processing/wait/file');

        $this->marker->onStartLoop($state);
    }

    public function testOnStartLoopWhenNotEnabled()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'track_loop_state' => false,
        ]);

        $this->fs
            ->expects($this->never())
            ->method('rename');
        $this->fs
            ->expects($this->never())
            ->method('remove');

        $this->marker->onStartLoop($state);
    }

    public function testOnSuccessLoopWhenNotRemoveOnSuccess()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'track_loop_state' => true,
            'track_loop_state_remove_on_success' => false,
        ]);
        $state->setContext('file_finder_current', '/tmp/file');

        $this->fs
            ->expects($this->once())
            ->method('rename')
            ->with('/tmp/_processing/wait/file', '/tmp/_processing/success/file');
        $this->fs
            ->expects($this->never())
            ->method('remove');

        $this->marker->onSuccessLoop($state);
    }

    public function testOnSuccessLoopWhenRemoveOnSuccess()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'track_loop_state' => true,
            'track_loop_state_remove_on_success' => true,
        ]);
        $state->setContext('file_finder_current', '/tmp/file');

        $this->fs
            ->expects($this->once())
            ->method('remove')
            ->with('/tmp/_processing/wait/file');
        $this->fs
            ->expects($this->once())
            ->method('rename')
            ->with('/tmp/file', '/tmp/_processing/wait/file');

        $this->marker->onStartLoop($state);
        $this->marker->onSuccessLoop($state);
    }

    public function testOnSuccessWhenNotEnabled()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'track_loop_state' => false,
        ]);

        $this->fs
            ->expects($this->never())
            ->method('rename');
        $this->fs
            ->expects($this->never())
            ->method('remove');

        $this->marker->onSuccessLoop($state);
    }

    public function testOnFailedLoop()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'track_loop_state' => true,
            'track_loop_state_remove_on_success' => false,
        ]);
        $state->setContext('file_finder_current', '/tmp/file');

        $this->fs
            ->expects($this->once())
            ->method('rename')
            ->with('/tmp/_processing/wait/file', '/tmp/_processing/failed/file');

        $this->marker->onFailedLoop($state);
    }

    public function testOnFailedLoopWhenNotEnabled()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'track_loop_state' => false,
        ]);

        $this->fs
            ->expects($this->never())
            ->method('rename');
        $this->fs
            ->expects($this->never())
            ->method('remove');

        $this->marker->onFailedLoop($state);
    }
}