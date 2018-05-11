<?php

namespace Darkilliant\ProcessBundle\Step;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ProcessBundle\State\ProcessState;

class PredefinedDataStep extends AbstractConfigurableStep
{
    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired('data');

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        $state->setData($state->getOptions()['data'] ?? null);
        $this->describe($state);
    }

    public function describe(ProcessState $state)
    {
        $state->info('use predefined data for debug', ['data' => $state->getData()]);
    }
}
