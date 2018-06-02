<?php

namespace Darkilliant\ProcessBundle\ProcessNotifier;

use Darkilliant\ProcessBundle\Console\ProgressBar;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\IterableStepInterface;
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

    public function onStartProcess(ProcessState $state, IterableStepInterface $step)
    {
        if (!$state->getOptions()['progress_bar']) {
            return null;
        }

        $count = $step->count($state);

        if (!$count) {
            return null;
        }

        $this->progressBar->create($count, get_class($step));
    }

    public function onUpdateProcess(ProcessState $state, IterableStepInterface $step)
    {
        if (!$state->getOptions()['progress_bar']) {
            return;
        }

        $this->progressBar->setProgress($step->getProgress($state));
    }

    public function onEndProcess(ProcessState $state)
    {
        if (!$state->getOptions()['progress_bar']) {
            return;
        }

        $this->progressBar->finish();
    }
}
