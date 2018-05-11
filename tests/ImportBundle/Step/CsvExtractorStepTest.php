<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Step;

use Darkilliant\ImportBundle\Extractor\CsvExtractor;
use Darkilliant\ImportBundle\Step\CsvExtractorStep;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvExtractorStepTest extends TestCase
{
    /** @var CsvExtractorStep */
    private $step;

    /** @var CsvExtractor|MockObject */
    private $extractor;

    public function setUp()
    {
        $this->extractor = $this->createMock(CsvExtractor::class);
        $this->step = new CsvExtractorStep($this->extractor);
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
            'filepath' => 'file.csv',
            'delimiter' => ';',
            'colums_names' => null,
        ]);

        $this->extractor
            ->expects($this->once())
            ->method('extract')
            ->with('file.csv', ';', null)
            ->willReturn(new \ArrayIterator());

        $this->step->execute($state);
    }

    public function testValid()
    {
        $iterator = new \ArrayIterator([['color' => 'red'], ['color' => 'blue']]);
        $this->extractor
            ->expects($this->once())
            ->method('extract')
            ->with('file.csv', ';', null)
            ->willReturn($iterator);

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['filepath' => 'file.csv', 'delimiter' => ';', 'colums_names' => null]);

        $this->step->execute($state);
        $this->assertTrue($this->step->valid($state));
        $this->assertEquals('file.csv', $state->getContext('filepath'));
    }

    public function testNext()
    {
        $iterator = new \ArrayIterator([['color' => 'red'], ['color' => 'blue']]);
        $this->extractor
            ->expects($this->once())
            ->method('extract')
            ->with('file.csv', ';', null)
            ->willReturn($iterator);

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['filepath' => 'file.csv', 'delimiter' => ';', 'colums_names' => null]);

        $this->step->execute($state);

        $this->step->next($state);
        $this->assertEquals(['color' => 'red'], $state->getData());
        $this->assertEquals(0, $state->getContext('line_csv'));

        $this->step->next($state);
        $this->assertEquals(['color' => 'blue'], $state->getData());
        $this->assertEquals(1, $state->getContext('line_csv'));
    }

    public function testDescribe()
    {
        $state = new ProcessState(
            [],
            $logger = $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions([
            'filepath' => 'file.csv',
        ]);

        $logger
            ->expects($this->once())
            ->method('log')
            ->with(
                LogLevel::INFO,
                'iterate on each line of {filepath} and transform into array',
                ['filepath' => 'file.csv']
            );

        $this->step->describe($state);
    }
}