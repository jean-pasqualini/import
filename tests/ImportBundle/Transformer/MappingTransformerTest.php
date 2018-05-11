<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Transformer;

use Darkilliant\ImportBundle\Registry\TransformerRegistry;
use Darkilliant\ImportBundle\Transformer\MappingTransformer;
use Darkilliant\ImportBundle\Transformer\TransformerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MappingTransformerTest extends TestCase
{
    /** @var MappingTransformer */
    private $transformer;

    /**
     * @var TransformerRegistry|MockObject
     */
    private $transformerRegistry;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $this->transformerRegistry = $this->createMock(TransformerRegistry::class);
        $this->transformer = new MappingTransformer($this->transformerRegistry);
    }

    public function testTransform()
    {
        $upperCaseTransformer = $this->createMock(TransformerInterface::class);
        $upperCaseTransformer
            ->expects($this->once())
            ->method('validate')
            ->willReturn(true);
        $upperCaseTransformer
            ->expects($this->once())
            ->method('transform')
            ->with('une maison bleu')
            ->willReturn('UNE MAISON BLEU');

        $this->transformerRegistry
            ->expects($this->once())
            ->method('get')
            ->with('uppercase')
            ->willReturn($upperCaseTransformer);


        $this->assertEquals(
            ['title' => 'UNE MAISON BLEU'],
            $this->transformer->transform([], [
                'title' => [
                    'value' => 'une maison bleu',
                    'transformers' => ['uppercase'],
                ]
            ])
        );
    }
}