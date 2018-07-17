<?php

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\State\ProcessState;

interface IterableStepInterface extends MonitorableStepInterface
{
    public function next(ProcessState $state);

    public function valid(ProcessState $state);

    public function onSuccessLoop(ProcessState $state);

    public function onFailedLoop(ProcessState $state);
}
