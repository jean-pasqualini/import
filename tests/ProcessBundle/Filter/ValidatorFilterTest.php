<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Filter;

use Darkilliant\ProcessBundle\Filter\ValidatorFilter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorFilterTest extends TestCase
{
    /** @var ValidatorFilter */
    private $filter;

    /** @var ValidatorInterface|MockObject */
    private $validator;

    protected function setUp()
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->filter = new ValidatorFilter($this->validator);
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->filter->configureOptionResolver($optionResolver)
        );
    }

    public function testIsAcceptWhenValid()
    {
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([]));

        $this->assertEquals(true, $this->filter->isAccept(5, [
            'validator' => NotNull::class,
            'options' => [],
            'groups' => [],
        ]));
    }

    public function testExceptionWhenValidatorIsNotConstraint()
    {
        $this->expectException(\Exception::class);

        $this->filter->isAccept(5, [
            'validator' => \stdClass::class,
            'options' => [],
            'groups' => [],
        ]);
    }

    public function testIsAcceptWhenInValid()
    {
        $violationList = new ConstraintViolationList(
            [
                new ConstraintViolation('error', '', [], '', '', '')
            ]
        );

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn($violationList);

        $this->assertEquals(false, $this->filter->isAccept(5, [
            'validator' => NotNull::class,
            'options' => [],
            'groups' => [],
        ]));
    }
}