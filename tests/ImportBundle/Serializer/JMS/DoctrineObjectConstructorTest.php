<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Serializer\JMS;

use App\Entity\Category;
use App\Entity\Product;
use Darkilliant\ImportBundle\Resolver\EntityResolver;
use Darkilliant\ImportBundle\Serializer\JMS\DoctrineObjectConstructor;
use Darkilliant\ImportBundle\Serializer\Symfony\EntityNormalizer;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;
use PhpCollection\Map;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class DoctrineObjectConstructorTest extends TestCase
{
    /** @var DoctrineObjectConstructor */
    private $normalizer;

    /**
     * @var EntityResolver
     */
    private $entityResolver;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $this->entityResolver = $this->createMock(EntityResolver::class);

        $this->normalizer = new DoctrineObjectConstructor(
            $this->createMock(ObjectConstructorInterface::class),
            $this->entityResolver,
            [
                Product::class => ['ean'],
            ]
        );
    }

    private function mockContext()
    {
        $context = $this->createMock(DeserializationContext::class);
        $attributes = $this->createMock(Map::class);
        $attributes
            ->expects($this->any())
            ->method('all')
            ->willReturn([]);

        $context->attributes = $attributes;

        return $context;
    }

    public function testDenormalize()
    {
        $product = new Product();
        $product->setEan('aaa');

        $this->entityResolver
            ->expects($this->once())
            ->method('resolve')
            ->with(Product::class, ['ean' => 'aaa'])
            ->willReturn($product);

        $classMetadata = new ClassMetadata(Product::class);

        $entity = $this->normalizer->construct(
            $this->createMock(VisitorInterface::class),
            $classMetadata,
            ['ean' => 'aaa'],
            [],
            $this->mockContext()
        );

        $this->assertInstanceOf(Product::class, $entity);
        $this->assertEquals($product, $entity);
    }

    public function testReturnNullWhenNormalizeWithClassHasNoConfigResolver()
    {
        $classMetadata = new ClassMetadata(self::class);

        $entity = $this->normalizer->construct(
            $this->createMock(VisitorInterface::class),
            $classMetadata,
            ['ean' => 'aaa'],
            [],
            $this->mockContext()
        );

        $this->assertNull($entity);
    }

    public function testReturnNewClassWhenDataHasNotFieldMappedInConfigResolver()
    {
        $classMetadata = new ClassMetadata(Product::class);

        $entity = $this->normalizer->construct(
            $this->createMock(VisitorInterface::class),
            $classMetadata,
            ['autre' => 'aaa'],
            [],
            $this->mockContext()
        );

        $this->assertInstanceOf(Product::class, $entity);
        $this->assertEquals(new Product(), $entity);
    }

    public function testReturnNewClassWhenNotFoundInDatabase()
    {
        $this->entityResolver
            ->expects($this->once())
            ->method('resolve')
            ->with(Product::class, ['ean' => 'aaa'])
            ->willReturn(null);

        $classMetadata = new ClassMetadata(Product::class);

        $entity = $this->normalizer->construct(
            $this->createMock(VisitorInterface::class),
            $classMetadata,
            ['ean' => 'aaa'],
            [],
            $this->mockContext()
        );

        $this->assertInstanceOf(Product::class, $entity);
        $this->assertEquals(new Product(), $entity);
    }
}