<?php

namespace Darkilliant\ProcessBundle\Step;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ProcessBundle\State\ProcessState;

class LaunchProcessStep extends AbstractConfigurableStep
{
    public function execute(ProcessState $state)
    {
        $state->info('launch process {process}', ['process' => $state->getOptions()['process']]);

        $process = $state->getStepRunner()->buildConfigurationProcess($state->getOptions()['process']);

        $state->getStepRunner()->run($process, $state->getOptions()['context'], $state->isDryRun());
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['process']);
        $resolver->setDefault('context', []);

        return parent::configureOptionResolver($resolver);
    }
}
