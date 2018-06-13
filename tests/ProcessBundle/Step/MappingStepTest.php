<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\MappingStep;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MappingStepTest extends TestCase
{
    /**
     * @var MappingStep
     */
    private $step;

    public function setUp()
    {
        $this->step = new MappingStep();
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->step->configureOptionResolver($optionResolver)
        );
    }

    public function testExecuteWithoutInitData()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions(['init_data' => null, 'mapping' => [
            'title' => 'maison'
        ]]);

        $this->step->execute($state);

        $this->assertEquals(
            ['title' => 'maison'],
            $state->getData()
        );
    }

    public function testExecuteWithInitData()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $stepRunner = $this->createMock(StepRunner::class)
        );
        $state->setOptions(['init_data' => ['id' => 3], 'mapping' => [
            'title' => 'maison'
        ]]);

        $this->step->execute($state);

        $this->assertEquals(
            ['id' => 3, 'title' => 'maison'],
            $state->getData()
        );
    }
}
