<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Step;

use Darkilliant\ImportBundle\Extractor\XmlExtractor;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;
use Darkilliant\ProcessBundle\Step\IterableStepInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class XmlExtractorStep extends AbstractConfigurableStep implements IterableStepInterface
{
    private $extractor;

    public function __construct(XmlExtractor $extractor)
    {
        $this->extractor = $extractor;
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['filepath', 'node_name']);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        $state->setIterator($this->extractor->extract(
            $state->getOptions()['filepath'],
            $state->getOptions()['node_name']
        ));
    }

    public function next(ProcessState $state)
    {
        $state->setData($state->getIterator()->current());

        $state->setContext('item_index', $state->getIterator()->key());

        $state->getIterator()->next();
    }

    public function getProgress(ProcessState $state)
    {
        return $state->getIterator()->key();
    }

    public function valid(ProcessState $state)
    {
        return $state->getIterator()->valid();
    }

    public function count(ProcessState $state)
    {
        $file = $state->getOptions()['filepath'];
        $node = '<'.$state->getOptions()['node_name'];

        if (!file_exists($file) || !is_file($file)) {
            return 0;
        }

        $cat = (strpos($file, '.gz') !== false) ? 'zcat' : 'cat';
        $count = `$cat $file | grep "$node" | wc -l`;

        if ((int) $count < 1) {
            return 0;
        }

        return (int) $count;
    }
}