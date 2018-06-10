<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Filter;

use Darkilliant\ProcessBundle\Filter\MappingFilter;
use Darkilliant\ProcessBundle\Filter\ValueFilter;
use Darkilliant\ProcessBundle\Registry\FilterRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MappingFilterTest extends TestCase
{
    /** @var MappingFilter */
    private $mappingFilter;

    /** @var FilterRegistry|MockObject */
    private $filterRegistry;

    protected function setUp()
    {
        $this->filterRegistry = $this->createMock(FilterRegistry::class);
        $this->mappingFilter = new MappingFilter($this->filterRegistry);
    }

    public function testIsAcceptWhenFilterReturnTrue()
    {
        $filter = $this->createMock(ValueFilter::class);
        $filter
            ->expects($this->once())
            ->method('isAccept')
            ->willReturn(true);

        $this->filterRegistry
            ->expects($this->once())
            ->method('get')
            ->with('value')
            ->willReturn($filter);

        $isAccepted = $this->mappingFilter->isAccept([
            [
                'type' => 'value',
                'value' => 'a',
                'valid_when_return' => true,
                'options' => ['expected' => 'a']
            ]
        ]);
        $this->assertTrue($isAccepted);
    }

    public function testIsAcceptWhenFilterReturnFalse()
    {
        $filter = $this->createMock(ValueFilter::class);
        $filter
            ->expects($this->once())
            ->method('isAccept')
            ->with('a', ['expected' => 'a'])
            ->willReturn(false);

        $this->filterRegistry
            ->expects($this->once())
            ->method('get')
            ->with('value')
            ->willReturn($filter);

        $isAccepted = $this->mappingFilter->isAccept([
            [
                'type' => 'value',
                'value' => 'a',
                'valid_when_return' => true,
                'options' => ['expected' => 'a']
            ]
        ]);
        $this->assertFalse($isAccepted);
    }
}