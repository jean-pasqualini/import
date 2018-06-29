<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Filter;

use Darkilliant\ProcessBundle\Filter\RegexFilter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegexFilterTest extends TestCase
{
    /** @var RegexFilter */
    private $filter;

    protected function setUp()
    {
        $this->filter = new RegexFilter();
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
        $this->assertEquals(false, $this->filter->isAccept('123', ['pattern' => '/^[0-9]{2}$/i']));
        $this->assertEquals(true, $this->filter->isAccept('12', ['pattern' => '/^[0-9]{2}$/i']));
    }
}