<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Serializer\JMS;

use App\Entity\Category;
use App\Entity\Product;
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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class DoctrineObjectConstructorTest extends TestCase
{
    /** @var DoctrineObjectConstructor */
    private $normalizer;

    /** @var ManagerRegistry|MockObject */
    private $managerRegistry;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $this->em = $this->createMock(EntityManager::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->managerRegistry
            ->expects($this->any())
            ->method('getManagerForClass')
            ->with(Product::class)
            ->willReturn($this->em);

        $this->normalizer = new DoctrineObjectConstructor(
            $this->managerRegistry,
            $this->createMock(ObjectConstructorInterface::class),
            [
                Product::class => ['ean'],
            ]
        );
    }

    public function testDenormalize()
    {
        $product = new Product();
        $product->setEan('aaa');

        $repository = $this->createMock(EntityRepository::class);

        $this->em
            ->expects($this->once())
            ->method('getRepository')
            ->with(Product::class)
            ->willReturn($repository);

        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['ean' => 'aaa'])
            ->willReturn($product);

        $classMetadata = new ClassMetadata(Product::class);

        $entity = $this->normalizer->construct(
            $this->createMock(VisitorInterface::class),
            $classMetadata,
            ['ean' => 'aaa'],
            [],
            $this->createMock(DeserializationContext::class)
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
            $this->createMock(DeserializationContext::class)
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
            $this->createMock(DeserializationContext::class)
        );

        $this->assertInstanceOf(Product::class, $entity);
        $this->assertEquals(new Product(), $entity);
    }

    public function testReturnNewClassWhenNotFoundInDatabase()
    {
        $repository = $this->createMock(EntityRepository::class);

        $this->em
            ->expects($this->once())
            ->method('getRepository')
            ->with(Product::class)
            ->willReturn($repository);


        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['ean' => 'aaa'])
            ->willReturn(null);

        $classMetadata = new ClassMetadata(Product::class);

        $entity = $this->normalizer->construct(
            $this->createMock(VisitorInterface::class),
            $classMetadata,
            ['ean' => 'aaa'],
            [],
            $this->createMock(DeserializationContext::class)
        );

        $this->assertInstanceOf(Product::class, $entity);
        $this->assertEquals(new Product(), $entity);
    }
}