<?php

namespace Darkilliant\ProcessBundle\ProcessNotifier;

use Darkilliant\ProcessBundle\Console\ProgressBar;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\MonitorableStepInterface;
use Darkilliant\ProcessBundle\Step\StepInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 * Class ProgressBarProcessNotifier
 */
class ProgressBarProcessNotifier implements EventSubscriberInterface, ProcessNotifierInterface
{
    /** @var ProgressBar */
    private $progressBar;

    public function __construct(ProgressBar $progressBar)
    {
        $this->progressBar = $progressBar;
        $this->progressBar->setOutput(new NullOutput());
    }

    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => ['onCommand', 255],
        ];
    }

    public function onCommand(ConsoleEvent $event)
    {
        $this->progressBar->setOutput($event->getOutput());
    }

    public function onStartProcess(ProcessState $state, StepInterface $step)
    {
        return;
    }

    public function onStartIterableProcess(ProcessState $state, StepInterface $step)
    {
        if (!$step instanceof MonitorableStepInterface) {
            return null;
        }

        if (!$state->getOptions()['progress_bar']) {
            return null;
        }

        $count = $step->count($state);

        if (!$count) {
            return null;
        }

        $this->progressBar->create($count, get_class($step));
    }

    public function onUpdateIterableProcess(ProcessState $state, StepInterface $step)
    {
        if (!$step instanceof MonitorableStepInterface) {
            return null;
        }

        if (!$state->getOptions()['progress_bar']) {
            return;
        }

        $this->progressBar->setProgress($step->getProgress($state));
    }

    public function onEndProcess(ProcessState $state, StepInterface $step)
    {
        if (!$step instanceof MonitorableStepInterface) {
            return null;
        }

        if (!$state->getOptions()['progress_bar']) {
            return;
        }

        $this->progressBar->finish();
    }

    public function onExecutedProcess(ProcessState $state, StepInterface $step)
    {
    }
}
