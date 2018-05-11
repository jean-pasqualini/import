<?php

namespace Tests\Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;
use Darkilliant\ImportBundle\Transformer\ContainKeysTransformer;
use PHPUnit\Framework\TestCase;

class ContainKeysTranformerTest extends TestCase
{
    /** @var ContainKeysTransformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new ContainKeysTransformer();
    }

    public function testTransform()
    {
        $this->assertEquals([], $this->transformer->transform([]));
    }

    public function testSuccesfullValidate()
    {
        $this->assertTrue($this->transformer->validate(
            [
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ],
            'input',
            ['keys' => ['a', 'b']]
        ));
    }

    public function testFailValidate()
    {
        $this->expectException(TransformationException::class);
        $this->transformer->validate(
            [
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ],
            'input',
            ['keys' => ['a', 'b', 'd']]
        );
    }
}