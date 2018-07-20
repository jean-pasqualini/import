<?php

namespace Darkilliant\ProcessBundle\ProcessNotifier;

use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\StepInterface;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractProcessNotifier implements ProcessNotifierInterface
{
    public function onStartProcess(ProcessState $state, StepInterface $step)
    {
        return;
    }

    public function onStartIterableProcess(ProcessState $state, StepInterface $step)
    {
        return;
    }

    public function onUpdateIterableProcess(ProcessState $state, StepInterface $step)
    {
        return;
    }

    public function onEndProcess(ProcessState $state, StepInterface $step)
    {
        return;
    }

    public function onExecutedProcess(ProcessState $state, StepInterface $step)
    {
        return;
    }

    public function onSuccessLoop(ProcessState $state, StepInterface $step)
    {
        return;
    }

    public function onFailedLoop(ProcessState $state, StepInterface $step)
    {
        return;
    }

    public function onStartRunner(ProcessState $state)
    {
        return;
    }

    public function onEndRunner(ProcessState $state, bool $successfull)
    {
        return;
    }
}
