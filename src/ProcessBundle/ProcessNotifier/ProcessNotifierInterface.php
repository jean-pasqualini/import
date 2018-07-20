<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\ProcessNotifier;

use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\StepInterface;

interface ProcessNotifierInterface
{
    public function onStartProcess(ProcessState $state, StepInterface $step);

    public function onStartIterableProcess(ProcessState $state, StepInterface $step);

    public function onUpdateIterableProcess(ProcessState $state, StepInterface $step);

    public function onEndProcess(ProcessState $state, StepInterface $step);

    public function onExecutedProcess(ProcessState $state, StepInterface $step);

    public function onSuccessLoop(ProcessState $state, StepInterface $step);

    public function onFailedLoop(ProcessState $state, StepInterface $step);

    public function onStartRunner(ProcessState $state);

    public function onEndRunner(ProcessState $state, bool $successfull);
}
