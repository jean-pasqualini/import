<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

class RegexFilter extends AbstractFilter
{
    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['pattern']);

        return parent::configureOptionResolver($resolver);
    }

    public function isAccept($value, array $options): bool
    {
        return preg_match($options['pattern'], $value) > 0;
    }
}
