<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Step;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ProcessBundle\State\ProcessState;

abstract class AbstractConfigurableStep implements StepInterface
{
    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['progress_bar', 'breaker', 'breaker_max_iteration', 'breaker_max_time', 'breaker_sleep_between']);
        $resolver->setDefault('progress_bar', false);
        $resolver->setDefault('breaker', false);
        $resolver->setDefault('breaker_max_iteration', null);
        $resolver->setDefault('breaker_sleep_between', 0);
        $resolver->setDefault('breaker_max_time', null);

        return $resolver;
    }

    public function finalize(ProcessState $state)
    {
        return;
    }

    public function describe(ProcessState $state)
    {
        $state->info(sprintf('run step %s', get_class($this)));
    }

    public function count(ProcessState $state)
    {
        return;
    }

    public function getProgress(ProcessState $state)
    {
        return 0;
    }

    public static function isDeprecated(): bool
    {
        return false;
    }

    public function onSuccessLoop(ProcessState $state)
    {
        return;
    }

    public function onFailedLoop(ProcessState $state)
    {
        return;
    }
}
