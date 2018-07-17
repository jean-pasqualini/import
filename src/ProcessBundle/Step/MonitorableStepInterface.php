<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\State\ProcessState;

interface MonitorableStepInterface
{
    public function count(ProcessState $state);

    public function getProgress(ProcessState $state);
}
