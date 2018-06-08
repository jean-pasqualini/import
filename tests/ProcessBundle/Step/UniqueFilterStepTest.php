<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\UniqueFilterStep;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UniqueFilterStepTest extends TestCase
{
    /** @var UniqueFilterStep */
    private $step;

    public function setUp()
    {
        $this->step = new UniqueFilterStep();
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->step->configureOptionResolver($optionResolver)
        );
    }

    public function testNotExecuteWhenDataIsNotArray()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['fields' => ['name'], 'data' => null]);

        $state->setData(1);
        $this->assertNull($this->step->execute($state));
    }

    public function testSkipWhenDoublon()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['fields' => ['name'], 'data' => null]);

        $state->setData(['name' => 'john']);
        $state->markSuccess();
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_OK, $state->getResult());

        $state->setData(['name' => 'john']);
        $state->markSuccess();
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_SKIP, $state->getResult());

        $state->setData(['name' => 'jules']);
        $state->markSuccess();
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_OK, $state->getResult());

        $this->assertNull($this->step->finalize($state));

        $state->setData(['name' => 'john']);
        $state->markSuccess();
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_OK, $state->getResult());
    }
}
