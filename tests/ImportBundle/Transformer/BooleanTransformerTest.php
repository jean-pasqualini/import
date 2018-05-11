<?php

namespace Tests\Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;
use Darkilliant\ImportBundle\Transformer\BooleanTransformer;
use PHPUnit\Framework\TestCase;

class BooleanTransformerTest extends TestCase
{
    /** @var BooleanTransformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new BooleanTransformer();
    }

    public function testTransform()
    {
        $this->assertEquals(true, $this->transformer->transform('1'));
        $this->assertEquals(true, $this->transformer->transform(1));
        $this->assertEquals(true, $this->transformer->transform(true));

        $this->assertEquals(false, $this->transformer->transform(false));
        $this->assertEquals(false, $this->transformer->transform('0'));
        $this->assertEquals(false, $this->transformer->transform(0));
    }

    public function testSuccesfullValidate()
    {
        $this->assertTrue($this->transformer->validate('1'));
        $this->assertTrue($this->transformer->validate(1));
        $this->assertTrue($this->transformer->validate(true));

        $this->assertTrue($this->transformer->validate(false));
        $this->assertTrue($this->transformer->validate('0'));
        $this->assertTrue($this->transformer->validate(0));
    }

    public function testFailValidate()
    {
        $this->expectException(TransformationException::class);
        $this->transformer->validate('');
    }
}