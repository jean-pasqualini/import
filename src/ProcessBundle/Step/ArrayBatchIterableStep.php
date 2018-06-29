<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\State\ProcessState;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArrayBatchIterableStep extends AbstractConfigurableStep
{
    private $data = [];

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired('batch_count');

        return parent::configureOptionResolver($resolver); // TODO: Change the autogenerated stub
    }

    public function execute(ProcessState $state)
    {
        $this->data[] = $state->getData();

        if (count($this->data) >= $state->getOptions()['batch_count'] || $state->getLoop()['last'] ?? false) {
            $state->setData($this->data);
            $this->data = [];

            return;
        }

        $state->markIgnore();
    }
}
