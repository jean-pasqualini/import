<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ProcessBundle\Resolver;

use Darkilliant\ProcessBundle\Resolver\OptionDynamicValueResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;

class OptionDynamicValueResolverTest extends TestCase
{
    /** @var OptionDynamicValueResolver */
    private $resolver;

    public function setUp()
    {
        $this->resolver = new OptionDynamicValueResolver(PropertyAccess::createPropertyAccessor());
    }

    public function testResolve()
    {
        $optionsResolved = $this->resolver->resolve(
            [
                'colors' => ['@[data][color]', '@!data->color', 4, []],
                'file' => '@[context][filepath]',
            ],
            [
            'data' => ['color' => 'red'],
            'context' => ['filepath' => 'file']
            ]
        );

        $this->assertEquals(
            ['colors' => ['red', 'red', 4, []], 'file' => 'file'],
            $optionsResolved
        );
    }
}