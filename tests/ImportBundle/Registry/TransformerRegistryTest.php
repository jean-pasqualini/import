<?php

namespace Tests\Darkilliant\ImportBundle\Registry;

use Darkilliant\ImportBundle\Registry\TransformerRegistry;
use Darkilliant\ImportBundle\Transformer\AbstractTransformer;
use PHPUnit\Framework\TestCase;

class TransformerRegistryTest extends TestCase
{
    /** @var TransformerRegistry */
    private $registry;

    public function setUp()
    {
        $this->registry = new TransformerRegistry();
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetWhenTransformerExists()
    {
        $transformer = $this->createMock(AbstractTransformer::class);

        $this->registry->add('demo', $transformer);

        $this->assertEquals($transformer, $this->registry->get('demo'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetWhenTransformerNotExists()
    {
        $this->expectException(\TypeError::class);

        $this->registry->get('demo');
    }
}