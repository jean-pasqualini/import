<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\State;

use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ProcessStateTest extends TestCase
{
    /** @var ProcessState */
    private $state;

    /** @var StepRunner|MockObject */
    private $runner;

    /** @var LoggerInterface|MockObject */
    private $logger;

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->runner = $this->createMock(StepRunner::class);
        $this->state = new ProcessState(
            [],
            $this->logger,
            $this->runner
        );
    }

    public function testConstructor()
    {
        $this->assertEquals($this->runner, $this->state->getStepRunner());
        $this->assertEquals($this->logger, $this->state->getLogger());
        $this->assertEquals([], $this->state->getRawContext());
        $this->assertEquals(null, $this->state->getResult());
    }

    public function testIterator()
    {
        $iterator = new \ArrayIterator();
        $this->state->setIterator($iterator);

        $this->assertEquals($iterator, $this->state->getIterator());
    }

    public function testDuplicate()
    {
        $this->state->setData(['color' => 'red']);
        $duplicatedState = $this->state->duplicate();
        $this->state->setData(['color' => 'green']);

        $this->assertNotEquals($duplicatedState, $this->state);
        $this->assertEquals(['color' => 'red'], $duplicatedState->getData());
    }

    public function testMarkIgnore()
    {
        $this->state->markIgnore();
        $this->assertEquals(ProcessState::RESULT_SKIP, $this->state->getResult());
    }

    public function testMarkSuccess()
    {
        $this->state->markSuccess();
        $this->assertEquals(ProcessState::RESULT_OK, $this->state->getResult());
    }

    public function testMarkFail()
    {
        $this->state->markFail();
        $this->assertEquals(ProcessState::RESULT_KO, $this->state->getResult());
    }
}