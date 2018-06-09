<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\ValidateObjectStep;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidateObjectStepTest extends TestCase
{
    /** @var ValidateObjectStep */
    private $step;

    /** @var ValidatorInterface|MockObject */
    private $validator;

    public function setUp()
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->step = new ValidateObjectStep($this->validator);
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->step->configureOptionResolver($optionResolver)
        );
    }

    public function testExecuteWithValidObject()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['groups' => []]);
        $state->markSuccess();

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([]));

        $this->step->execute($state);

        $this->assertEquals(ProcessState::RESULT_OK, $state->getResult());
    }

    public function testExecuteWithInvalidObject()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['groups' => []]);
        $state->markSuccess();

        $violationList = new ConstraintViolationList(
            [
                new ConstraintViolation('error', '', [], '', '', '')
            ]
        );

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn($violationList);

        $this->step->execute($state);

        $this->assertEquals(ProcessState::RESULT_KO, $state->getResult());
    }
}