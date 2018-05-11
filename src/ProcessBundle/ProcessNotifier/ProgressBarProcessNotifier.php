<?php

namespace Darkilliant\ProcessBundle\ProcessNotifier;

use Darkilliant\ProcessBundle\Console\ProgressBar;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\IterableStepInterface;
use Darkilliant\ProcessBundle\Step\StepInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProgressBarProcessNotifier implements EventSubscriberInterface
{
    /** @var ProgressBar */
    private $progressBar;

    private $currentStep;

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
        if (!$step instanceof IterableStepInterface) {
            return;
        }

        $count = $step->count($state);

        if (!$count || !$state->getOptions()['progress_bar']) {
            return;
        }

        $this->progressBar->create($count, get_class($step));
        $this->currentStep = $step;
    }

    public function onUpdateProcess(ProcessState $state, StepInterface $step)
    {
        if (!$this->currentStep) {
            return;
        }
        $this->progressBar->setProgress($this->currentStep->getProgress($state));
    }

    public function onEndProcess(ProcessState $state, StepInterface $step)
    {
        if (!$this->currentStep) {
            return;
        }

        $this->currentStep = null;
        $this->progressBar->finish();
    }
}
