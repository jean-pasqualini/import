<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\TargetResolver;

use App\Entity\Product;
use Darkilliant\ImportBundle\TargetResolver\ArrayTargetResolver;
use Darkilliant\ImportBundle\TargetResolver\DoctrineTargetResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ArrayTargetResolverTest extends TestCase
{
    /** @var ArrayTargetResolver */
    private $resolver;

    /** @var DoctrineTargetResolver|MockObject */
    private $doctrineResolver;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $this->doctrineResolver = $this->createMock(DoctrineTargetResolver::class);

        $this->resolver = new ArrayTargetResolver($this->doctrineResolver);
    }

    /**
     * @throws \Exception
     */
    public function testResolve()
    {
        $this->doctrineResolver
            ->expects($this->once())
            ->method('resolve')
            ->with([
                'entity_class' => Product::class
            ])
            ->willReturn('__resolved__');

        $data = $this->resolver->resolve(['a' => 1, 'b' => 2], [
            'a' => [
                'entity_class' => Product::class,
            ]
        ]);

        $this->assertInternalType('array', $data);
        $this->assertEquals(['a' => '__resolved__', 'b' => 2], $data);
    }
}