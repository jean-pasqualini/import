<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Step;

use Darkilliant\ImportBundle\Extractor\ExcelSplitter;
use Darkilliant\ImportBundle\Step\SplitExcelStep;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SplitExcelStepTest extends TestCase
{
    /** @var SplitExcelStep */
    private $step;

    /** @var ExcelSplitter|MockObject */
    private $spitter;

    public function setUp()
    {
        $this->spitter = $this->createMock(ExcelSplitter::class);
        $this->step = new SplitExcelStep($this->spitter);
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->step->configureOptionResolver($optionResolver)
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function testExecute()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'filepath' => 'file.xls',
        ]);

        $this->spitter
            ->expects($this->once())
            ->method('split')
            ->with('file.xls')
            ->willReturn([]);

        $this->step->execute($state);
    }

    public function testValid()
    {
        $this->spitter
            ->expects($this->once())
            ->method('split')
            ->with('file.xls')
            ->willReturn(['tab_a.csv', 'tab_b.csv']);

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['filepath' => 'file.xls']);

        $this->step->execute($state);
        $this->assertTrue($this->step->valid($state));
    }

    public function testNext()
    {
        $this->spitter
            ->expects($this->once())
            ->method('split')
            ->with('file.xls')
            ->willReturn(['tab_a.csv', 'tab_b.csv']);

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['filepath' => 'file.xls']);

        $this->step->execute($state);
        $this->step->next($state);

        $this->assertEquals('tab_a.csv', $state->getData());
    }

    public function testDescribe()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'filepath' => 'file.xls',
        ]);

        $logger
            ->expects($this->once())
            ->method('log')
            ->with(
                LogLevel::INFO,
                'split excel file {filepath} into one csv file for each tab',
                ['filepath' => 'file.xls']
            );

        $this->step->describe($state);
    }
}
