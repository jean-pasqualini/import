<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

class StrposFilter extends AbstractFilter
{
    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['substring']);

        return parent::configureOptionResolver($resolver);
    }

    public function isAccept($value, array $options): bool
    {
        return false !== strpos($value, $options['substring']);
    }
}
