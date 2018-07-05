<?php

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\State\ProcessState;

interface IterableStepInterface
{
    public function next(ProcessState $state);

    public function valid(ProcessState $state);

    public function count(ProcessState $state);

    public function getProgress(ProcessState $state);

    public function onSuccessLoop(ProcessState $state);

    public function onFailedLoop(ProcessState $state);
}
