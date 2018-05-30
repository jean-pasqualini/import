<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\ArrayBatchIterableStep;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArrayBatchIterableStepTest extends TestCase
{
    /** @var ArrayBatchIterableStep */
    private $step;

    protected function setUp()
    {
        $this->step = new ArrayBatchIterableStep();
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
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'batch_count' => 2,
        ]);

        $state->markSuccess();
        $state->loop(1, 7, false);
        $state->setData(1);
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_SKIP, $state->getResult());

        $state->markSuccess();
        $state->loop(2, 7, false);
        $state->setData(2);
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_OK, $state->getResult());
        $this->assertEquals([1, 2], $state->getData());

        $state->markSuccess();
        $state->loop(3, 7, false);
        $state->setData(3);
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_SKIP, $state->getResult());

        $state->markSuccess();
        $state->loop(4, 7, false);
        $state->setData(4);
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_OK, $state->getResult());
        $this->assertEquals([3, 4], $state->getData());

        $state->markSuccess();
        $state->loop(5, 7, false);
        $state->setData(5);
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_SKIP, $state->getResult());

        $state->markSuccess();
        $state->loop(6, 7, false);
        $state->setData(6);
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_OK, $state->getResult());
        $this->assertEquals([5, 6], $state->getData());

        $state->markSuccess();
        $state->loop(7, 7, true);
        $state->setData(7);
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_OK, $state->getResult());
        $this->assertEquals([7], $state->getData());
    }
}