<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Filter;

use Darkilliant\ProcessBundle\Filter\ValueFilter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValueFilterTest extends TestCase
{
    /** @var ValueFilter */
    private $filter;

    protected function setUp()
    {
        $this->filter = new ValueFilter();
    }

    public function testConfigureOptions()
    {
        $optionResolver = $this->createMock(OptionsResolver::class);

        $this->assertInstanceOf(
            OptionsResolver::class,
            $this->filter->configureOptionResolver($optionResolver)
        );
    }

    public function testIsAccept()
    {
        $this->assertEquals(false, $this->filter->isAccept('aa', ['expected' => 'bb']));
        $this->assertEquals(false, $this->filter->isAccept(1, ['expected' => '1']));

        $this->assertEquals(true, $this->filter->isAccept('aa', ['expected' => 'aa']));
        $this->assertEquals(true, $this->filter->isAccept(1, ['expected' => 1]));
    }
}