<?php

namespace Darkilliant\ProcessBundle\ProcessNotifier;

use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\StepInterface;

class ChainProcessNotifier implements ProcessNotifierInterface
{
    /** @var ProcessNotifierInterface[] */
    private $notifiers = [];

    public function add(ProcessNotifierInterface $notifier)
    {
        $this->notifiers[] = $notifier;
    }

    public function onExecutedProcess(ProcessState $state, StepInterface $step)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->onExecutedProcess($state, $step);
        }
    }

    public function onStartProcess(ProcessState $state, StepInterface $step)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->onStartProcess($state, $step);
        }
    }

    public function onEndProcess(ProcessState $state, StepInterface $step)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->onEndProcess($state, $step);
        }
    }

    public function onStartIterableProcess(ProcessState $state, StepInterface $step)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->onStartIterableProcess($state, $step);
        }
    }

    public function onUpdateIterableProcess(ProcessState $state, StepInterface $step)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->onUpdateIterableProcess($state, $step);
        }
    }

    public function onSuccessLoop(ProcessState $state, StepInterface $step)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->onSuccessLoop($state, $step);
        }
    }

    public function onFailedLoop(ProcessState $state, StepInterface $step)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->onFailedLoop($state, $step);
        }
    }

    public function onStartRunner(ProcessState $state)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->onStartRunner($state);
        }
    }

    public function onEndRunner(ProcessState $state, bool $successfull)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->onEndRunner($state, $successfull);
        }
    }
}
