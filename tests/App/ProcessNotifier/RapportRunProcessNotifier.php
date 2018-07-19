<?php

namespace App\ProcessNotifier;

use Darkilliant\ProcessBundle\ProcessNotifier\AbstractProcessNotifier;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\StepInterface;

class RapportRunProcessNotifier extends AbstractProcessNotifier
{
    private $loopSuccess = 0;
    private $loopFail = 0;

    public function onSuccessLoop(ProcessState $state, StepInterface $step)
    {
        $this->loopSuccess++;
    }

    public function onFailedLoop(ProcessState $state, StepInterface $step)
    {
        $this->loopFail++;
    }

    public function onEndRunner(ProcessState $state, bool $successfull)
    {
        $state->info(
            'rapport run ({success} success, {fail} fail)',
            [
                'success' => $this->loopSuccess,
                'fail' => $this->loopFail
            ]
        );
    }
}