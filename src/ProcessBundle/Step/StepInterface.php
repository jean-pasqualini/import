<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Step;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ProcessBundle\State\ProcessState;

interface StepInterface
{
    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver;

    public function execute(ProcessState $state);

    public function finalize(ProcessState $state);

    public function describe(ProcessState $state);

    public static function isDeprecated(): bool;
}
