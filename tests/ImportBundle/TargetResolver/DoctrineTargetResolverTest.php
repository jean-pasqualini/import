<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\TargetResolver;

use App\Entity\Product;
use Darkilliant\ImportBundle\TargetResolver\DoctrineTargetResolver;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DoctrineTargetResolverTest extends TestCase
{
    /** @var DoctrineTargetResolver */
    private $resolver;

    /** @var EntityManagerInterface|MockObject */
    private $em;

    protected function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->resolver = new DoctrineTargetResolver($this->em);
    }

    /**
     * @throws \Exception
     */
    public function testResolveWithBadStrategy()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('factory fail');

        $this->resolver->resolve([
            'entityClass' => 'App\Entity\Product',
            'create' => true,
            'strategy' => [
                'name' => 'unknow',
                'options' => []
            ],
        ]);
    }

    public function testInstanciateEntityWhenNotFoundInDatabase()
    {
        $repository = $this->createMock(EntityRepository::class);

        $this->em
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository);

        $entity = $this->resolver->resolve([
            'entityClass' => Product::class,
            'create' => true,
            'strategy' => [
                'name' => 'findOneBy',
                'options' => []
            ],
        ]);

        $this->assertInstanceOf(Product::class, $entity);
        $this->assertEquals(new Product(), $entity);
    }

    public function testEntityWhenFoundInDatabaseWithStrategyFindOneBy()
    {
        $product = new Product();
        $product->setTitle('one product');

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($product);

        $this->em
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repository);

        $entity = $this->resolver->resolve([
            'entityClass' => Product::class,
            'create' => true,
            'strategy' => [
                'name' => 'findOneBy',
                'options' => []
            ],
        ]);

        $this->assertInstanceOf(Product::class, $entity);
        $this->assertEquals($product, $entity);
    }


    public function testEntityWhenFoundInDatabaseWithStrategyFind()
    {
        $product = new Product();
        $product->setTitle('one product');

        $this->em
            ->expects($this->once())
            ->method('getReference')
            ->willReturn($product);

        $entity = $this->resolver->resolve([
            'entityClass' => Product::class,
            'create' => true,
            'strategy' => [
                'name' => 'find',
                'options' => [1]
            ],
        ]);

        $this->assertInstanceOf(Product::class, $entity);
        $this->assertEquals($product, $entity);
    }
}