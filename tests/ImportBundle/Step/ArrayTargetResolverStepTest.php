<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Step;

use Darkilliant\ImportBundle\Step\ArrayTargetResolverStep;
use Darkilliant\ImportBundle\TargetResolver\ArrayTargetResolver;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArrayTargetResolverStepTest extends TestCase
{
    /** @var ArrayTargetResolverStep */
    private $step;

    /** @var ArrayTargetResolver|MockObject */
    private $resolver;

    public function setUp()
    {
        $this->resolver = $this->createMock(ArrayTargetResolver::class);
        $this->step = new ArrayTargetResolverStep($this->resolver);
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
            'resolve_mapping' => [],
        ]);
        $state->setData(['boutique' => ['id' => 1]]);

        $this->resolver
            ->expects($this->once())
            ->method('resolve')
            ->with(['boutique' => ['id' => 1]], [])
            ->willReturn(['color' => 'red']);

        $this->step->execute($state);
        $this->assertEquals(
            ['color' => 'red'],
            $state->getData()
        );
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
                'replace keys ({resolve}) by entity doctrine',
                ['resolve' => '']
            );

        $this->step->describe($state);
    }
}