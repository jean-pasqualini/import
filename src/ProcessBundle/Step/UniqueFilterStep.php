<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Exception\NonUniqueException;
use Darkilliant\ProcessBundle\State\ProcessState;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UniqueFilterStep extends AbstractConfigurableStep
{
    private $collector;

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['fields', 'data', 'throw_error']);
        $resolver->setDefault('data', null);
        $resolver->setDefault('throw_error', false);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        $fields = $state->getOptions()['fields'];
        $data = $state->getOptions()['data'] ?: $state->getData();

        if (!is_array($data)) {
            return;
        }

        $collected = '';
        foreach ($fields as $field) {
            $collected .= '|'.$data[$field];
        }

        if (isset($this->collector[$collected])) {
            if ($state->getOption('throw_error')) {
                throw new NonUniqueException(
                    sprintf('skipped, this data is not unique (%s)', $collected)
                );
            }

            $state->warning(
                'skipped, this data is not unique',
                ['collected' => $collected]
            );

            $state->markIgnore();

            return;
        }

        $this->collector[$collected] = 1;
    }

    public function finalize(ProcessState $state)
    {
        $this->collector = [];
    }
}
