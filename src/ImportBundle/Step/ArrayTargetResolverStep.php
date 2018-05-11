<?php

namespace Darkilliant\ImportBundle\Step;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ImportBundle\TargetResolver\ArrayTargetResolver;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;

class ArrayTargetResolverStep extends AbstractConfigurableStep
{
    /** @var ArrayTargetResolver */
    private $resolver;

    public function __construct(ArrayTargetResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param ProcessState $state
     *
     * @throws \Exception
     *
     * @return array
     */
    public function execute(ProcessState $state)
    {
        $data = $this->resolver->resolve($state->getData(), $state->getOptions()['resolve_mapping']);

        $state->setData($data);
    }

    public function describe(ProcessState $state)
    {
        $state->info('replace keys ({resolve}) by entity doctrine', [
            'resolve' => implode(', ', array_keys($state->getOptions()['resolve_mapping'] ?? [])),
        ]);
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['resolve_mapping']);

        return parent::configureOptionResolver($resolver);
    }
}
