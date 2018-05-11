<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Step;

use Darkilliant\ImportBundle\Exception\TransformationException;
use Darkilliant\ImportBundle\Step\MappingTransformerStep;
use Darkilliant\ImportBundle\Transformer\MappingTransformer;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MappingTransformerStepTest extends TestCase
{
    /** @var MappingTransformerStep */
    private $step;

    /** @var MappingTransformer|MockObject */
    private $mappingTransformer;

    public function setUp()
    {
        $this->mappingTransformer = $this->createMock(MappingTransformer::class);
        $this->step = new MappingTransformerStep($this->mappingTransformer);
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->step->configureOptionResolver($optionResolver)
        );
    }

    public function testSuccessfulExecute()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'mapping' => [],
        ]);

        $this->mappingTransformer
            ->expects($this->once())
            ->method('transform')
            ->with([], [])
            ->willReturn(['color' => 'red']);

        $this->step->execute($state);
        $this->assertEquals(
            ['color' => 'red'],
            $state->getData()
        );
    }

    public function testFailedExecuteWithTransformException()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'mapping' => [],
        ]);

        $this->mappingTransformer
            ->expects($this->once())
            ->method('transform')
            ->with([], [])
            ->willThrowException(new TransformationException('color is bad'));

        $this->step->execute($state);

        $this->assertEquals(ProcessState::RESULT_KO, $state->getResult());
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
            ->with(
                LogLevel::INFO,
                'run step Darkilliant\ImportBundle\Step\MappingTransformerStep',
                []
            );

        $this->step->describe($state);
    }
}