<?php

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Filter\MappingFilter;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\FilterStep;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterStepTest extends TestCase
{
    /** @var FilterStep */
    private $step;

    /** @var MappingFilter|MockObject */
    private $mappingFilter;

    protected function setUp()
    {
        $this->mappingFilter = $this->createMock(MappingFilter::class);
        $this->step = new FilterStep($this->mappingFilter);
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->step->configureOptionResolver($optionResolver)
        );
    }

    public function testExecuteSkipWhenMappingFilterReturnFalse()
    {
        $this->mappingFilter
            ->expects($this->once())
            ->method('isAccept')
            ->with([])
            ->willReturn(false);

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['filters' => []]);

        $state->markSuccess();
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_SKIP, $state->getResult());
    }

    public function testExecuteContinueWhenMappingFilterReturnTrue()
    {
        $this->mappingFilter
            ->expects($this->once())
            ->method('isAccept')
            ->with([])
            ->willReturn(true);

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['filters' => []]);

        $state->markSuccess();
        $this->step->execute($state);
        $this->assertEquals(ProcessState::RESULT_OK, $state->getResult());
    }

}