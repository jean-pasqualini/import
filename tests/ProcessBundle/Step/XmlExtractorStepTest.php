<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Step;

use Darkilliant\ImportBundle\Extractor\XmlExtractor;
use Darkilliant\ImportBundle\Step\XmlExtractorStep;
use Darkilliant\ProcessBundle\Runner\StepRunner;
use Darkilliant\ProcessBundle\State\ProcessState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class XmlExtractorStepTest extends TestCase
{
    /** @var XmlExtractorStep */
    private $step;

    /** @var XmlExtractor|MockObject */
    private $extractor;

    public function setUp()
    {
        $this->extractor = $this->createMock(XmlExtractor::class);
        $this->step = new XmlExtractorStep($this->extractor);
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
        $state->setOptions(['filepath' => 'test.xml', 'node_name' => 'truc']);

        $this->extractor
            ->expects($this->once())
            ->method('extract')
            ->with('test.xml', 'truc')
            ->willReturn(new \ArrayIterator(['oui' => 'oui']));

        $this->step->execute($state);

        $this->assertEquals(['oui' => 'oui'], $state->getIterator()->getArrayCopy());
    }

    public function testNext()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['filepath' => 'test.xml', 'node_name' => 'truc']);
        $state->setIterator(new \ArrayIterator([
            ['item' => 1],
            ['item' => 2],
            ['item' => 3],
        ]));

        $this->step->next($state);
        $this->assertEquals(['item' => 1], $state->getData());

        $this->step->next($state);
        $this->assertEquals(['item' => 2], $state->getData());
    }

    public function testValid()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['filepath' => 'test.xml', 'node_name' => 'truc']);
        $state->setIterator(new \ArrayIterator([
            ['item' => 1],
            ['item' => 2],
        ]));

        $this->assertTrue($this->step->valid($state));
        $this->step->next($state);

        $this->assertTrue($this->step->valid($state));
        $this->step->next($state);

        $this->assertFalse($this->step->valid($state));
    }

    public function testCount()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['filepath' => 'test.xml', 'node_name' => 'truc']);

        $this->assertEquals(0, $this->step->count($state));
    }

    public function testCountWhenBadGzip()
    {
        $filepath = sprintf('/tmp/%s.gz', uniqid('darkilliant_process_xml_extractor'));
        file_put_contents($filepath, 'hello');

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['filepath' => $filepath, 'node_name' => 'truc']);

        $this->assertEquals(0, $this->step->count($state));
    }

    public function testCountWhenGoodFile()
    {
        $filepath = sprintf('/tmp/%s.txt', uniqid('darkilliant_process_xml_extractor'));
        file_put_contents($filepath, '<truc'.PHP_EOL.'world'.PHP_EOL);

        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['filepath' => $filepath, 'node_name' => 'truc']);

        $this->assertEquals(1, $this->step->count($state));
    }

    public function testGetProgress()
    {
        $state = new ProcessState(
            [],
            $this->createMock(LoggerInterface::class),
            $this->createMock(StepRunner::class)
        );
        $state->setOptions(['filepath' => 'test.xml', 'node_name' => 'truc']);
        $state->setIterator(new \ArrayIterator([
            ['item' => 1],
            ['item' => 2],
            ['item' => 3],
        ]));

        $this->assertEquals(0, $this->step->getProgress($state));
        $this->step->next($state);

        $this->assertEquals(1, $this->step->getProgress($state));
        $this->step->next($state);
    }
}