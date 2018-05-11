<?php

namespace Tests\Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;
use Darkilliant\ImportBundle\Transformer\FloatTransformer;
use PHPUnit\Framework\TestCase;

class FloatTransformerTest extends TestCase
{
    /** @var FloatTransformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new FloatTransformer();
    }

    public function testTransform()
    {
        $this->assertEquals(5, $this->transformer->transform('5'));
        $this->assertEquals(5.5, $this->transformer->transform('5.5'));
    }

    public function testSuccesfullValidate()
    {
        $this->assertTrue($this->transformer->validate('5'));
        $this->assertTrue($this->transformer->validate(5));
    }

    public function testFailValidate()
    {
        $this->expectException(TransformationException::class);
        $this->transformer->validate('');
    }
}