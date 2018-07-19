<?php

declare(strict_types=1);

namespace App\Step;

use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;

class FailStep extends AbstractConfigurableStep
{
    public function execute(ProcessState $state)
    {
        throw new \Exception('error throw');
        return;
    }
}