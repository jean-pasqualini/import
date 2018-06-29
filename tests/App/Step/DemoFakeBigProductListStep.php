<?php

namespace App\Step;

use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;
use Darkilliant\ProcessBundle\Step\IterableStepInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemoFakeBigProductListStep extends AbstractConfigurableStep implements IterableStepInterface
{
    private $count;

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['count']);
        $resolver->setDefaults([
            'count' => 150000,
        ]);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        $this->count = $state->getOptions()['count'];

        $data = [];
        for ($i = 1; $i<= $this->count; $i ++) {
            $data[] = [
                'ean' => sprintf('ean_%s', $i),
                'name' => sprintf('name_%s', $i)
            ];
        }

        $state->setIterator(new \ArrayIterator($data));
    }

    public function count(ProcessState $state)
    {
        return $state->getIterator()->count();
    }

    public function valid(ProcessState $state)
    {
        return $state->getIterator()->valid();
    }

    public function getProgress(ProcessState $state)
    {
        return $state->getIterator()->key();
    }

    public function next(ProcessState $state)
    {
        $state->setData($state->getIterator()->current());
        $state->getIterator()->next();

        return;
    }
}