<?php

namespace Darkilliant\ImportBundle\Step;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ImportBundle\Persister\DoctrinePersister;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;

class DoctrinePersisterStep extends AbstractConfigurableStep
{
    /** @var DoctrinePersister */
    private $persister;

    public function __construct(DoctrinePersister $persister)
    {
        $this->persister = $persister;
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['batch_count', 'whitelist_clear', 'blacklist_clear']);
        $resolver->setDefault('whitelist_clear', []);
        $resolver->setDefault('blacklist_clear', []);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        if (!$state->isDryRun()) {
            $this->persister->persist(
                $state->getData(),
                $state->getOptions()['batch_count'],
                $state->getOptions()['whitelist_clear'],
                $state->getOptions()['blacklist_clear']
            );

            $this->describe($state);
        }
    }

    public function finalize(ProcessState $state)
    {
        $this->persister->finalize();
    }

    public function describe(ProcessState $state)
    {
        $state->info('persist entity doctrine and flush by group of {batch_count} elements', $state->getOptions());
    }
}
