<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;
use Darkilliant\ImportBundle\Transformer\IntegerTransformer;
use PHPUnit\Framework\TestCase;

class IntegerTransformerTest extends TestCase
{
    /** @var IntegerTransformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new IntegerTransformer();
    }

    public function testTransform()
    {
        $this->assertEquals(5, $this->transformer->transform('5'));
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