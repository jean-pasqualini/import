<?php

namespace Tests\Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;
use Darkilliant\ImportBundle\Transformer\DateTimeTransformer;
use PHPUnit\Framework\TestCase;

class DateTimeTransformerTest extends TestCase
{
    /** @var DateTimeTransformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new DateTimeTransformer();
    }

    public function testTransform()
    {
        $this->assertEquals('value', $this->transformer->transform('value'));
    }

    public function testSuccesfullValidate()
    {
        $this->assertTrue($this->transformer->validate(new \DateTime()));
    }

    public function testFailValidate()
    {
        $this->expectException(TransformationException::class);
        $this->transformer->validate('');
    }
}