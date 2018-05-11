<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;
use Darkilliant\ImportBundle\Transformer\StringTransformer;
use PHPUnit\Framework\TestCase;

class StringTransformerTest extends TestCase
{
    /** @var StringTransformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new StringTransformer();
    }

    public function testTransform()
    {
        $this->assertEquals('home', $this->transformer->transform('home'));
    }

    public function testSuccesfullValidate()
    {
        $this->assertTrue($this->transformer->validate('home'));
    }

    public function testFailValidate()
    {
        $this->expectException(TransformationException::class);
        $this->transformer->validate(4);
    }
}