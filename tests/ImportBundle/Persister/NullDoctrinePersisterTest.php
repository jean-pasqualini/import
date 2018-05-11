<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Persister;

use Darkilliant\ImportBundle\Persister\NullDoctrinePersister;
use PHPUnit\Framework\TestCase;

class NullDoctrinePersisterTest extends TestCase
{
    /**
     * @var NullDoctrinePersister
     */
    private $persister;

    public function setUp()
    {
        $this->persister = new NullDoctrinePersister();
    }

    public function testPersist()
    {
        $this->assertNull($this->persister->persist(new \stdClass()));
    }

    public function testFinalize()
    {
        $this->assertNull($this->persister->finalize());
    }
}