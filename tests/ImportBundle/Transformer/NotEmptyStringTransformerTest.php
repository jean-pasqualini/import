<?php
/**
 * Created by PhpStorm.
 * User: jpasqualini
 * Date: 17/05/18
 * Time: 13:28
 */

namespace Tests\Darkilliant\ImportBundle\Transformer;


use Darkilliant\ImportBundle\Exception\TransformationException;
use Darkilliant\ImportBundle\Transformer\NotEmptyStringTransformer;
use PHPUnit\Framework\TestCase;

class NotEmptyStringTransformerTest extends TestCase
{
    /** @var NotEmptyStringTransformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new NotEmptyStringTransformer();
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
        $this->transformer->validate('');
    }
}