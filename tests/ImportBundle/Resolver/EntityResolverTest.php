<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Resolver;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Tag;
use App\Fake\FakeQuery;
use Darkilliant\ImportBundle\Resolver\EntityResolver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class EntityResolverTest extends TestCase
{
    /** @var EntityResolver|MockObject */
    private $resolver;

    /** @var ManagerRegistry|MockObject */
    private $managerRegistry;

    /** @var EntityManager|MockObject */
    private $entityManager;

    /** @var EntityRepository|MockObject */
    private $repository;

    /** @var QueryBuilder|MockObject */
    private $queryBuilder;

    /** @var UnitOfWork|MockObject */
    private $uow;

    public function setUp()
    {
        $this->resolver = new EntityResolver(
            $this->managerRegistry = $this->createMock(ManagerRegistry::class),
            [
                Product::class => ['ean'],
                Category::class => ['name'],
            ],
            [
                Category::class => true,
            ]
        );
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->repository = $this->createMock(EntityRepository::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->uow = $this->createMock(UnitOfWork::class);
    }

    public function testResolveWithoutCache()
    {
        $this->managerRegistry
            ->expects($this->once())
            ->method('getManagerForClass')
            ->with(Product::class)
            ->willReturn($this->entityManager);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(Product::class)
            ->willReturn($this->repository);

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['ean' => 5])
            ->willReturn(new Product());

        $entity = $this->resolver->resolve(Product::class, ['ean' => 5]);

        $this->assertEquals(new Product(), $entity);
    }

    public function testResolveWithoutWhere()
    {
        $entity = $this->resolver->resolve(Product::class, []);
        $this->assertNull($entity);
        $entity = $this->resolver->resolve(Tag::class, []);
        $this->assertNull($entity);
    }

    public function testResolveWithCache()
    {
        $query = $this->createMock(FakeQuery::class);
        $query
            ->expects($this->once())
            ->method('getArrayResult')
            ->willReturn([
                ['id' => 10, 'name' => 'choco'],
            ]);

        $this->managerRegistry
            ->expects($this->any())
            ->method('getManagerForClass')
            ->with(Category::class)
            ->willReturn($this->entityManager);

        $this
            ->queryBuilder
            ->expects($this->once())
            ->method('select')
            ->with('o.id, o.name')
            ->willReturn($this->queryBuilder);

        $this
            ->queryBuilder
            ->expects($this->once())
            ->method('from')
            ->with(Category::class)
            ->willReturn($this->queryBuilder);

        $this
            ->queryBuilder
            ->expects($this->once())
            ->method('indexBy')
            ->with('o', 'o.id')
            ->willReturn($this->queryBuilder);

        $this
            ->queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->entityManager
            ->expects($this->any())
            ->method('getUnitOfWork')
            ->willReturn($this->uow);

        $this->uow
            ->expects($this->any())
            ->method('createEntity')
            ->with(Category::class, ['id' => 10], [])
            ->willReturn(new Category());

        $classMetadata = new ClassMetadata(Category::class);
        $classMetadata->reflFields = [
            'name' => null
        ];

        $this->entityManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        $entity = $this->resolver->resolve(Category::class, ['name' => 'choco']);

        $this->assertEquals(new Category(), $entity);

        $this->assertNull($this->resolver->resolve(Category::class, ['name' => 'choca']));

        $this->resolver->clear(Category::class);
    }

    public function testClear()
    {
        $this->assertNull($this->resolver->clear());
        $this->assertNull($this->resolver->clear(Product::class));
    }
}