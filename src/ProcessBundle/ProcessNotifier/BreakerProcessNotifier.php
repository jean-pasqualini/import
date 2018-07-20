<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\ProcessNotifier;

use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\StepInterface;

class BreakerProcessNotifier extends AbstractProcessNotifier
{
    private $breaker = null;

    private $count = 0;
    private $startedAt;
    private $timeElasped = 0;

    public function onStartProcess(ProcessState $state, StepInterface $step)
    {
        // TODO: Implement onStartProcess() method.
    }

    public function onStartIterableProcess(ProcessState $state, StepInterface $step)
    {
        $this->breaker = null;

        if (!$state->getOptions()['breaker']) {
            return;
        }

        $maxIteration = $state->getOptions()['breaker_max_iteration'];
        $maxTime = $state->getOptions()['breaker_max_time'];
        $sleepBetween = $state->getOptions()['breaker_sleep_between'];

        $this->breaker = ['max_time' => $maxTime, 'max_iteration' => $maxIteration, 'sleep_between' => $sleepBetween];

        $state->info('breaker', $this->breaker);

        $this->count = 0;
        $this->startedAt = time();
        $this->timeElasped = 0;
    }

    public function onUpdateIterableProcess(ProcessState $state, StepInterface $step)
    {
        if (!$this->breaker) {
            return;
        }

        $this->timeElasped = time() - $this->startedAt;
        ++$this->count;
        sleep($this->breaker['sleep_between']);

        if (!$this->isValid()) {
            $state->info('breaker stop current loop');
            $state->markBreak();

            return;
        }
    }

    public function onEndProcess(ProcessState $state, StepInterface $step)
    {
        // TODO: Implement onEndProcess() method.
    }

    public function onExecutedProcess(ProcessState $state, StepInterface $step)
    {
        // TODO: Implement onExecutedProcess() method.
    }

    public function onFailedLoop(ProcessState $state, StepInterface $step)
    {
        return;
    }

    public function onSuccessLoop(ProcessState $state, StepInterface $step)
    {
        return;
    }

    private function isValid()
    {
        if (null !== $this->breaker['max_time'] && $this->timeElasped >= $this->breaker['max_time']) {
            return false;
        }

        if (null !== $this->breaker['max_iteration'] && $this->count >= $this->breaker['max_iteration']) {
            return false;
        }

        return true;
    }
}
