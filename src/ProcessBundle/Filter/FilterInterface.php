<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface FilterInterface
{
    public function isAccept($value, array $options): bool;

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver;
}
