<?php

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\State\ProcessState;

class WhileStep extends AbstractConfigurableStep implements IterableStepInterface
{
    public function execute(ProcessState $state)
    {
        return;
    }

    public function valid(ProcessState $state)
    {
        return true;
    }

    public function next(ProcessState $state)
    {
        return;
    }

    public function count(ProcessState $state)
    {
        return 0;
    }

    public function getProgress(ProcessState $state)
    {
        return 0;
    }
}
