<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\State\ProcessState;

class IterateArrayStep extends AbstractConfigurableStep implements IterableStepInterface
{
    public function execute(ProcessState $state)
    {
        $state->setIterator(new \ArrayIterator($state->getData()));
    }

    public function next(ProcessState $state)
    {
        $state->setData($state->getIterator()->current());
        $state->getIterator()->next();
    }

    public function valid(ProcessState $state): bool
    {
        return $state->getIterator()->valid();
    }

    public function count(ProcessState $state)
    {
        return $state->getIterator()->count();
    }

    public function describe(ProcessState $state)
    {
        $state->info('Each one line of array of {count} lines', ['count' => 'X']);
    }

    public function getProgress(ProcessState $state)
    {
        return $state->getIterator()->key();
    }
}
