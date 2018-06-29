<?php

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\State\ProcessState;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WhileStep extends AbstractConfigurableStep implements IterableStepInterface
{
    public $count = 0;
    private $startedAt;
    private $timeElasped = 0;

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['sleep_between', 'max_time', 'max_iteration']);
        $resolver->setDefault('sleep_between', 1);
        $resolver->setDefault('max_time', null);
        $resolver->setDefault('max_iteration', null);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        if (null === $state->getOptions()['max_time'] && null === $state->getOptions()['max_iteration']) {
            $state->error('please set max_time or max_iteration');
            $state->markFail();

            return;
        }

        $state->info('while', $state->getOptions());

        $this->count = 0;
        $this->startedAt = time();
        $this->timeElasped = 0;
    }

    public function valid(ProcessState $state)
    {
        if (null !== $state->getOptions()['max_time'] && $this->timeElasped >= $state->getOptions()['max_time']) {
            return false;
        }

        if (null !== $state->getOptions()['max_iteration'] && $this->count >= $state->getOptions()['max_iteration']) {
            return false;
        }

        return true;
    }

    public function next(ProcessState $state)
    {
        $state->getLogger()->debug('next on while', [
            'count' => $this->count,
            'started_at' => $this->startedAt,
            'time_elapsed' => $this->timeElasped,
        ]);

        $this->timeElasped = time() - $this->startedAt;
        ++$this->count;
        sleep($state->getOptions()['sleep_between']);
    }

    public function count(ProcessState $state)
    {
        return $state->getOptions()['max_iteration'] ?: $state->getOptions()['max_time'] ?: 0;
    }

    public function getProgress(ProcessState $state)
    {
        if ($state->getOptions()['max_iteration']) {
            return $this->count;
        }

        if ($state->getOptions()['max_time']) {
            return $this->timeElasped;
        }

        return 0;
    }
}
