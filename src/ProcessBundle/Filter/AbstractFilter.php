<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractFilter implements FilterInterface
{
    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        return $resolver;
    }
}
