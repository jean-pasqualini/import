<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Exception\TransformationException;
use Darkilliant\ImportBundle\Transformer\RemoveEmptyInArrayTransformer;
use PHPUnit\Framework\TestCase;

class RemoveEmptyInArrayTransformerTest extends TestCase
{
    /**
     * @var RemoveEmptyInArrayTransformer
     */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new RemoveEmptyInArrayTransformer();
    }

    public function testTransform()
    {
        $this->assertEquals([0 => 'a', 2 => 'c'], $this->transformer->transform(['a', '', 'c']));
    }

    public function testSuccesfullValidate()
    {
        $this->assertTrue($this->transformer->validate([]));
    }
}