<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Serializer\Symfony;

use App\Entity\Category;
use App\Entity\Product;
use Darkilliant\ImportBundle\Resolver\EntityResolver;
use Darkilliant\ImportBundle\Serializer\Symfony\EntityNormalizer;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;

class EntityNormalizerTest extends TestCase
{
    /** @var EntityNormalizer */
    private $normalizer;

    /** @var EntityResolver|MockObject */
    private $resolver;

    /** @var Serializer|MockObject */
    private $serializer;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $this->resolver = $this->createMock(EntityResolver::class);
        $this->serializer = $this->createMock(Serializer::class);
        $this->serializer
            ->expects($this->any())
            ->method('denormalize')
            ->willReturnCallback(function($data, $class, $format, $context) {
               return $context['object_to_populate'];
            });

        $this->normalizer = new EntityNormalizer([
            Product::class => ['ean'],
        ], $this->resolver);
        $this->normalizer->setSerializer($this->serializer);
    }

    public function testDenormalize()
    {
        $product = new Product();
        $product->setEan('aaa');

        $this->resolver
            ->expects($this->once())
            ->method('resolve')
            ->with(Product::class)
            ->willReturn($product);

        $entity = $this->normalizer->denormalize(['ean' => 'aaa'], Product::class);

        $this->assertInstanceOf(Product::class, $entity);
        $this->assertEquals($product, $entity);
    }

    public function testReturnNullWhenNormalizeWithClassHasNoConfigResolver()
    {
        $entity = $this->normalizer->denormalize(['ean' => 'aaa'], Category::class);

        $this->assertEquals(new Category(), $entity);
    }

    public function testReturnNewClassWhenDataHasNotFieldMappedInConfigResolver()
    {
        $entity = $this->normalizer->denormalize(['title' => 'aaa'], Product::class);

        $this->assertInstanceOf(Product::class, $entity);
        $this->assertEquals(new Product(), $entity);
    }

    public function testReturnNewClassWhenNotFoundInDatabase()
    {
        $this->resolver
            ->expects($this->once())
            ->method('resolve')
            ->with(Product::class)
            ->willReturn(null);

        $entity = $this->normalizer->denormalize(['ean' => 'aaa'], Product::class);

        $this->assertInstanceOf(Product::class, $entity);
        $this->assertEquals(new Product(), $entity);
    }

    public function provideSupportNormalization()
    {
        yield 'not support when class is not in resolver config' => [
            [
                'params' => [[], Category::class],
                'expected' => false,
            ]
        ];

        yield 'not support when already_resolved' => [
            [
                'params' => [['_resolved' => true], Product::class],
                'expected' => false,
            ]
        ];

        yield 'not support when not array' => [
            [
                'params' => ['string', Product::class],
                'expected' => false,
            ]
        ];

        yield 'support when properly configured' => [
            [
                'params' => [[], Product::class],
                'expected' => true,
            ]
        ];
    }

    /**
     * @dataProvider provideSupportNormalization
     */
    public function testSupportDenormalization(array $config)
    {
        list ($data, $type) = $config['params'];

        $this->assertEquals(
            $config['expected'],
            $this->normalizer->supportsDenormalization($data, $type)
        );
    }
}