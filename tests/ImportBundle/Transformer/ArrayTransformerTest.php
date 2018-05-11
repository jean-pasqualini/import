<?php

namespace Tests\Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;
use Darkilliant\ImportBundle\Transformer\ArrayTransformer;
use PHPUnit\Framework\TestCase;

class ArrayTransformerTest extends TestCase
{
    /** @var ArrayTransformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new ArrayTransformer();
    }

    public function testTransform()
    {
        $this->assertEquals([], $this->transformer->transform([]));
    }

    public function testSuccesfullValidate()
    {
        $this->assertTrue($this->transformer->validate([]));
    }

    public function testFailValidate()
    {
        $this->expectException(TransformationException::class);
        $this->transformer->validate('');
    }
}