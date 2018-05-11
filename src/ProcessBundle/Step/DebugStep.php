<?php

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\State\ProcessState;

class DebugStep extends AbstractConfigurableStep
{
    public function execute(ProcessState $state)
    {
        $state->info('current data', ['data' => $state->getData()]);
    }

    public function describe(ProcessState $state)
    {
        $state->info('debug data');
    }
}
