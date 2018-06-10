<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Registry;

use Darkilliant\ProcessBundle\Filter\AbstractFilter;
use Darkilliant\ProcessBundle\Registry\FilterRegistry;
use PHPUnit\Framework\TestCase;

class FilterRegistryTest extends TestCase
{
    /** @var FilterRegistry */
    private $registry;

    public function setUp()
    {
        $this->registry = new FilterRegistry();
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetWhenFilterExists()
    {
        $filter = $this->createMock(AbstractFilter::class);

        $this->registry->add('demo', $filter);

        $this->assertEquals($filter, $this->registry->get('demo'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetWhenFilterNotExists()
    {
        $this->expectException(\TypeError::class);

        $this->registry->get('demo');
    }
}