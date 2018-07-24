<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Step;

use Darkilliant\ImportBundle\Persister\DoctrinePersister;
use Darkilliant\ImportBundle\Step\DoctrinePersisterStep;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DoctrinePersisterStepTest extends TestCase
{
    /** @var DoctrinePersisterStep */
    private $step;

    /** @var DoctrinePersister|MockObject */
    private $persister;

    public function setUp()
    {
        $this->persister = $this->createMock(DoctrinePersister::class);
        $this->step = new DoctrinePersisterStep($this->persister);
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
            'batch_count' => 20,
            'whitelist_clear' => [],
            'blacklist_clear' => []
        ]);
        $state->setData(new \stdClass());

        $this->persister
            ->expects($this->once())
            ->method('persist')
            ->with(new \stdClass(), 20, [], []);

        $this->step->execute($state);
    }

    public function testFinalize()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );

        $this->persister
            ->expects($this->once())
            ->method('finalize')
            ->with();

        $this->step->finalize($state);
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
                'persist entity doctrine and flush by group of {batch_count} elements',
                []
            );

        $this->step->describe($state);
    }
}