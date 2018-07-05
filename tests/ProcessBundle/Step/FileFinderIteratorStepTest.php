<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\LoopStateMarker\FileLoopStateMarker;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\FileFinderIteratorStep;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileFinderIteratorStepTest extends TestCase
{
    /** @var FileFinderIteratorStep|MockObject */
    private $step;

    /** @var FileLoopStateMarker|MockObject */
    private $fileLoopStateMarker;

    /** @var Finder|MockObject */
    private $finder;

    protected function setUp()
    {
        $this->finder = $this->createMock(Finder::class);
        $this->fileLoopStateMarker = $this->createMock(FileLoopStateMarker::class);
        $this->step = new FileFinderIteratorStep($this->fileLoopStateMarker, $this->finder);
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
        $this->finder
            ->expects($this->once())
            ->method('files')
            ->willReturn($this->finder);
        $this->finder
            ->expects($this->once())
            ->method('in')
            ->with('/tmp')
            ->willReturn($this->finder);
        $this->finder
            ->expects($this->exactly(2))
            ->method('depth')
            ->willReturn($this->finder);
        $this->finder
            ->expects($this->once())
            ->method('name')
            ->with('*.log')
            ->willReturn($this->finder);
        $this->finder
            ->expects($this->once())
            ->method('date')
            ->with('tomorow')
            ->willReturn($this->finder);

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'in' => '/tmp',
            'recursive' => false,
            'depth' => 4,
            'name' => '*.log',
            'date' => 'tomorow',
        ]);

        $this->step->execute($state);
    }

    public function testGetProgress()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $this->assertEquals(1, $this->step->getProgress($state));
    }

    public function testCount()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $this->assertEquals(1, $this->step->count($state));
    }

    public function testNext()
    {
        $this->finder
            ->expects($this->once())
            ->method('files')
            ->willReturn($this->finder);
        $this->finder
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([new \SplFileObject(__FILE__)]));

        $this->fileLoopStateMarker
            ->expects($this->once())
            ->method('onStartLoop');

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'in' => '/tmp',
            'recursive' => false,
            'depth' => 4,
            'name' => '*.log',
            'date' => 'tomorow',
        ]);

        $this->step->execute($state);
        $this->step->next($state);
        $this->step->valid($state);

        $this->assertEquals(__FILE__, $state->getContext('file_finder_current'));
        $this->assertEquals(__FILE__, $state->getData());
    }

    public function testOnSuccessLoop()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );

        $this->fileLoopStateMarker
            ->expects($this->once())
            ->method('onSuccessLoop');

        $this->step->onSuccessLoop($state);
    }

    public function testOnFailedLoop()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );

        $this->fileLoopStateMarker
            ->expects($this->once())
            ->method('onFailedLoop');

        $this->step->onFailedLoop($state);
    }
}