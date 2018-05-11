<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Extractor;

use Cocur\Slugify\Slugify;
use Darkilliant\ImportBundle\Extractor\ExcelSplitter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class ExcelSplitterTest extends TestCase
{
    /** @var ExcelSplitter */
    private $splitter;

    /** @var Slugify|MockObject */
    private $slugify;

    const OUTPUT_DIR = '/tmp/importbundle/splitcsv/';

    public function setUp()
    {
        $this->slugify = $this->createMock(Slugify::class);
        $this->splitter = new ExcelSplitter($this->slugify);
        $this->slugify
            ->expects($this->any())
            ->method('slugify')
            ->willReturnArgument(0);

        $fileSystem = new Filesystem();
        if ($fileSystem->exists(self::OUTPUT_DIR)) {
            $fileSystem->remove(self::OUTPUT_DIR);
        }

        $fileSystem->mkdir(self::OUTPUT_DIR);
        $fileSystem->copy(
            __DIR__.'/../../fixtures/xls/demo.xlsx',
            self::OUTPUT_DIR.'/demo.xlsx'
        );
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(ExcelSplitter::class, $this->splitter);
    }

    public function testSplit()
    {
        $this->splitter->split(self::OUTPUT_DIR.'/demo.xlsx');

        $files = glob(self::OUTPUT_DIR.'/*.csv');

        $this->assertGreaterThan(0, count($files));
        $this->assertEquals([
            self::OUTPUT_DIR.'/demo_color.csv',
            self::OUTPUT_DIR.'/demo_size.csv',
        ], $files);
    }
}