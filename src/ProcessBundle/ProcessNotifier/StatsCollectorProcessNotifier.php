<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\ProcessNotifier;

use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\StepInterface;

class StatsCollectorProcessNotifier extends AbstractProcessNotifier
{
    private $stats = [];
    private $resolvedStat = [];
    private $position = 0;
    private $enabled = false;

    public function onStartProcess(ProcessState $state, StepInterface $step)
    {
        if (!$this->enabled) {
            return;
        }

        if (!isset($this->stats[get_class($step)])) {
            $this->stats[get_class($step)] = [];
        }

        $this->stats[get_class($step)]['last_start'] = microtime(true) * 1000;
        if (!isset($this->stats[get_class($step)]['time'])) {
            $this->stats[get_class($step)]['time'] = [];
            $this->stats[get_class($step)]['real_time'] = [];
            $this->stats[get_class($step)]['position'] = ++$this->position;
            $this->stats[get_class($step)]['wait'] = [];
        } else {
            $this->stats[get_class($step)]['wait'][] = $this->stats[get_class($step)]['last_start'] - $this->stats[get_class($step)]['last_finish'];
        }
    }

    public function onStartIterableProcess(ProcessState $state, StepInterface $step)
    {
        return;
    }

    public function onUpdateIterableProcess(ProcessState $state, StepInterface $step)
    {
    }

    public function onEndProcess(ProcessState $state, StepInterface $step)
    {
    }

    public function onExecutedProcess(ProcessState $state, StepInterface $step)
    {
        if (!$this->enabled) {
            return;
        }

        $currentTime = (microtime(true) * 1000);
        $time = $currentTime - $this->stats[get_class($step)]['last_start'];
        $this->stats[get_class($step)]['time'][] = $time;
        $this->stats[get_class($step)]['last_finish'] = $currentTime;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function getData()
    {
        return $this->stats;
    }

    public function onFailedLoop(ProcessState $state, StepInterface $step)
    {
        return;
    }

    public function onSuccessLoop(ProcessState $state, StepInterface $step)
    {
        return;
    }
}
