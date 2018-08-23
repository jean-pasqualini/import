<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Extractor;

use Cocur\Slugify\Slugify;
use Darkilliant\ImportBundle\Extractor\CsvExtractor;
use PHPUnit\Framework\TestCase;

class CsvExtractorTest extends TestCase
{
    /** @var CsvExtractor */
    protected $extractor;

    /** @var string */
    private $filepath;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $slugify;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

    }

    /**
     * @throws \ReflectionException
     */
    protected function setUp()
    {
        parent::setUp();
        $this->slugify = $this->createMock(Slugify::class);
        $this->extractor = new CsvExtractor($this->slugify);
        $this->filepath = __DIR__ . '/../../fixtures/csv/product.csv';
    }

    public function testExtract()
    {
        $this->slugify->expects($this->any())
            ->method('slugify')
            ->willReturnArgument(0);
        $dataRows = $this->extractor->extract($this->filepath, ';');

        $this->assertEquals([
            1 => [
                'name' => 'savon',
                'price' => '2.32',
            ],

            2 => [
                'name' => 'lait',
                'price' => '1.5',
            ],
        ], iterator_to_array($dataRows));
    }

    public function testExtractWhenNotSkipFirstLine()
    {
        $this->slugify->expects($this->any())
            ->method('slugify')
            ->willReturnArgument(0);
        $dataRows = $this->extractor->extract($this->filepath, ';', null, false);

        $this->assertEquals([
            0 => [
                'name' => 'name',
                'price' => 'price',
            ],
            1 => [
                'name' => 'savon',
                'price' => '2.32',
            ],

            2 => [
                'name' => 'lait',
                'price' => '1.5',
            ],
        ], iterator_to_array($dataRows));
    }
}