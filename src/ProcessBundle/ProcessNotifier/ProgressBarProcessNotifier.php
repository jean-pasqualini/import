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

/**
 * @internal
 * Class ProgressBarProcessNotifier
 */
class ProgressBarProcessNotifier implements EventSubscriberInterface
{
    /** @var ProgressBar */
    private $progressBar;
    /** @var bool */
    private $infinite = false;

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
            return null;
        }

        if (!$state->getOptions()['progress_bar']) {
            return null;
        }

        $count = $step->count($state);
        $this->infinite = !$count;

        if ($this->infinite) {
            $count = 9999999;
        }

        $this->progressBar->create($count, get_class($step));
    }

    public function onUpdateProcess(ProcessState $state, StepInterface $step)
    {
        if (!$state->getOptions()['progress_bar']) {
            return;
        }

        $this->progressBar->setProgress($step->getProgress($state));
    }

    public function onEndProcess(ProcessState $state, StepInterface $step)
    {
        if (!$state->getOptions()['progress_bar'] || $this->infinite) {
            return;
        }

        $this->progressBar->finish();
    }
}
