<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Filter;

use Darkilliant\ProcessBundle\Filter\StrposFilter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StrposFilterTest extends TestCase
{
    /** @var StrposFilter */
    private $filter;

    protected function setUp()
    {
        $this->filter = new StrposFilter();
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
        $this->assertEquals(true, $this->filter->isAccept('une maison bleu', ['substring' => 'maison']));
        $this->assertEquals(false, $this->filter->isAccept('une maison bleu', ['substring' => 'bateau']));
    }
}