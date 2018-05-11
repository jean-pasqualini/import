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

        if ($state->getOptions()['share_state']) {
            $duplicateState = $state->duplicate();
            $state->getStepRunner()->runSteps(
                $duplicateState,
                $process->getSteps()
            );

            return;
        }

        $state->getStepRunner()->run($process, $state->getOptions()['context']);
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['process', 'share_state']);
        $resolver->setDefault('share_state', false);
        $resolver->setDefault('context', []);

        return parent::configureOptionResolver($resolver);
    }
}
