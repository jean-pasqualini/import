<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Extractor;

use Darkilliant\ImportBundle\Extractor\XmlExtractor;
use PHPUnit\Framework\TestCase;

class XmlExtractorTest extends TestCase
{
    /** @var XmlExtractor */
    private $extractor;

    public function setUp()
    {
        $this->extractor = new XmlExtractor();
    }

    public function testExtractWithNoGzipedXml()
    {
        $iterator = $this->extractor->extract(__DIR__.'/../../fixtures/xml/album.xml', 'album');

        $this->assertEquals('Name1', $iterator->current()['name'] ?? 'unknow');
        $iterator->next();
        $this->assertEquals('Name2', $iterator->current()['name'] ?? 'unknow');
        $iterator->next();
        $this->assertEquals(null, $iterator->current());
    }

    public function testExtractWithGzipedXml()
    {
        $iterator = $this->extractor->extract(__DIR__.'/../../fixtures/xml/album.xml.gz', 'album');

        $this->assertEquals('Name1', $iterator->current()['name'] ?? 'unknow');
        $iterator->next();
        $this->assertEquals('Name2', $iterator->current()['name'] ?? 'unknow');
    }
}