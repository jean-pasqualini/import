<?php

namespace Darkilliant\ProcessUIBundle\ProcessNotifier;

use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\IterableStepInterface;
use Darkilliant\ProcessBundle\Step\StepInterface;

class WebProcessNotifier
{
    private $handler;

    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    public function onStartProcess(ProcessState $state, StepInterface $step)
    {
        if (!$step instanceof IterableStepInterface) {
            return null;
        }

        if (!$state->getOptions()['progress_bar']) {
            return null;
        }

        $count = $step->count($state);

        if (!$count) {
            return null;
        }

        call_user_func_array($this->handler, [$count, 0]);
    }

    public function onUpdateProcess(ProcessState $state, StepInterface $step)
    {
        if (!$state->getOptions()['progress_bar']) {
            return;
        }
        /** @var IterableStepInterface $step */


        call_user_func_array($this->handler, [$step->count($state), $step->getProgress($state)]);
    }

    public function onEndProcess(ProcessState $state, StepInterface $step)
    {
        if (!$state->getOptions()['progress_bar']) {
            return;
        }

        call_user_func_array($this->handler, [$step->count($state), $step->count($state)]);
    }

}