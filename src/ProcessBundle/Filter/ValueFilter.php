<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ValueFilter extends AbstractFilter
{
    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['strict', 'expected']);

        return parent::configureOptionResolver($resolver);
    }

    public function isAccept($value, array $options): bool
    {
        return $value === $options['expected'];
    }
}
