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
}
