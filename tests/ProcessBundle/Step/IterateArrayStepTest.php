<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\IterateArrayStep;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class IterateArrayStepTest extends TestCase
{
    /** @var IterateArrayStep */
    private $step;

    public function setUp()
    {
        $this->step = new IterateArrayStep();
    }

    public function testExecute()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setData([
            ['item' => 1],
            ['item' => 2],
            ['item' => 3],
        ]);

        $this->step->execute($state);

        $this->assertInstanceOf(\ArrayIterator::class, $state->getIterator());
    }

    public function testNext()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setData([
            ['item' => 1],
            ['item' => 2],
            ['item' => 3],
        ]);

        $this->step->execute($state);

        $this->step->next($state);
        $this->assertEquals(['item' => 1], $state->getData());

        $this->step->next($state);
        $this->assertEquals(['item' => 2], $state->getData());
    }

    public function testValid()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setData([
            ['item' => 1],
            ['item' => 2],
        ]);

        $this->step->execute($state);

        $this->assertTrue($this->step->valid($state));
        $this->step->next($state);

        $this->assertTrue($this->step->valid($state));
        $this->step->next($state);

        $this->assertFalse($this->step->valid($state));
    }

    public function testCount()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setData([
            ['item' => 1],
            ['item' => 2],
            ['item' => 3],
        ]);

        $this->step->execute($state);

        $this->assertEquals(3, $this->step->count($state));
    }

    public function testGetProgress()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setData([
            ['item' => 1],
            ['item' => 2],
            ['item' => 3],
        ]);

        $this->step->execute($state);

        $this->assertEquals(0, $this->step->getProgress($state));
        $this->step->next($state);

        $this->assertEquals(1, $this->step->getProgress($state));
        $this->step->next($state);
    }

    public function testDescribe()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );

        $logger
            ->expects($this->once())
            ->method('log')
            ->with(LogLevel::INFO, 'Each one line of array of {count} lines', [
                'count' => 'X',
            ]);

        $this->step->describe($state);
    }
}