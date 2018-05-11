<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Persister;

use App\Entity\Product;
use Darkilliant\ImportBundle\Persister\DoctrinePersister;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DoctrinePersisterTest extends TestCase
{
    /**
     * @var DoctrinePersister
     */
    private $persister;

    /** @var EntityManagerInterface|MockObject */
    private $em;

    public function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->persister = new DoctrinePersister($this->em);
    }

    public function testPersist()
    {
        $this->assertNull($this->persister->persist(new \stdClass()));
    }

    public function testPersistWithBatchOne()
    {
        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with(new Product());

        $this->em
            ->expects($this->once())
            ->method('clear');

        $this->assertNull($this->persister->persist(new Product(), 1));
    }

    public function testFinalize()
    {
        $entity = new \stdClass();
        $this->persister->persist($entity);

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($entity);

        $this->em
            ->expects($this->once())
            ->method('clear');

        $this->assertNull($this->persister->finalize());
    }

    public function testFinalizeWithWhilelist()
    {
        $entity = new \stdClass();
        $this->persister->persist($entity, 20, [Product::class], []);

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($entity);

        $this->em
            ->expects($this->once())
            ->method('clear');

        $this->assertNull($this->persister->finalize());
    }

    public function testFinalizeWithBlackList()
    {
        $entity = new \stdClass();
        $this->persister->persist($entity, 20, [], [Product::class]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('unsupported blacklist');

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($entity);

        $this->em
            ->expects($this->never())
            ->method('clear');

        $this->persister->finalize();
    }
}