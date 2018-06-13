<?php

declare(strict_types=1);

namespace App\Step;


use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;

class DeprecatedStep extends AbstractConfigurableStep
{
    public function execute(ProcessState $state)
    {
        return;
    }

    public static function isDeprecated(): bool
    {
        return true;
    }
}