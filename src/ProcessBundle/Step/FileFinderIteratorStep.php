<?php

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\LoopStateMarker\FileLoopStateMarker;
use Darkilliant\ProcessBundle\State\ProcessState;
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileFinderIteratorStep extends AbstractConfigurableStep implements IterableStepInterface
{
    private $count = 1;
    private $progress = 1;
    /** @var FileLoopStateMarker */
    private $loopStateMarker;
    /** @var Finder */
    private $finder;

    public function __construct(FileLoopStateMarker $loopStateMarker, Finder $finder = null)
    {
        $this->loopStateMarker = $loopStateMarker;
        $this->finder = $finder;
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(
            ['in', 'recursive', 'depth', 'name', 'date', 'track_loop_state', 'track_loop_state_remove_on_success']
        );
        $resolver->setDefault('recursive', true);
        $resolver->setDefault('depth', null);
        $resolver->setDefault('name', null);
        $resolver->setDefault('date', null);
        $resolver->setDefault('track_loop_state', false);
        $resolver->setAllowedTypes('track_loop_state', 'bool');
        $resolver->setDefault('track_loop_state_remove_on_success', false);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        $finder = $this->getFinder();

        $finder->files()->in($state->getOptions()['in']);

        if (!$state->getOptions()['recursive']) {
            $finder->depth('< 1');
        }

        if ($state->getOptions()['depth']) {
            $depth = (array) $state->getOptions()['depth'];

            foreach ($depth as $eachDepth) {
                $finder->depth($eachDepth);
            }
        }

        if ($state->getOptions()['name']) {
            $nameFilder = (array) $state->getOptions()['name'];

            foreach ($nameFilder as $nameFilderDepth) {
                $finder->name($nameFilderDepth);
            }
        }

        if ($state->getOptions()['date']) {
            $dateFilder = (array) $state->getOptions()['date'];

            foreach ($dateFilder as $dateFilderDepth) {
                $finder->date($dateFilderDepth);
            }
        }

        $finder->exclude('_processing');

        $state->setIterator($this->each($finder));

        $this->count = $finder->count();
    }

    public function next(ProcessState $state)
    {
        /** @var \SplFileInfo $current */
        $current = $state->getIterator()->current();

        $state->setData($current->getPathname());
        $state->setContext('file_finder_current', $current->getPathname());

        $this->loopStateMarker->onStartLoop($state);

        $state->getIterator()->next();

        ++$this->progress;
    }

    public function valid(ProcessState $state)
    {
        return $state->getIterator()->valid();
    }

    public function getProgress(ProcessState $state)
    {
        return $this->progress;
    }

    public function count(ProcessState $state)
    {
        return $this->count;
    }

    public function onSuccessLoop(ProcessState $state)
    {
        $this->loopStateMarker->onSuccessLoop($state);
    }

    public function onFailedLoop(ProcessState $state)
    {
        $this->loopStateMarker->onFailedLoop($state);
    }

    private function getFinder()
    {
        return $this->finder ?: new Finder();
    }

    private function each(Finder $finder): \Traversable
    {
        foreach ($finder as $file) {
            yield $file;
        }
    }
}
