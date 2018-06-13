<?php

namespace Darkilliant\ProcessBundle\Step;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ProcessBundle\State\ProcessState;

class MappingStep extends AbstractConfigurableStep
{
    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['init_data', 'mapping']);
        $resolver->setDefault('init_data', null);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        $data = $state->getOptions()['init_data'];

        if (is_array($data)) {
            $data = array_merge($data, $state->getOptions()['mapping']);
        } else {
            $data = $state->getOptions()['mapping'];
        }

        $state->setData($data);
        $this->describe($state);
    }

    public function describe(ProcessState $state)
    {
        $state->info('map data');
    }
}
