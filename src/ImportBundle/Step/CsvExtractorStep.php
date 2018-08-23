<?php

namespace Darkilliant\ImportBundle\Step;

use Darkilliant\ImportBundle\Extractor\CsvExtractor;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;
use Darkilliant\ProcessBundle\Step\IterableStepInterface;

class CsvExtractorStep extends AbstractConfigurableStep implements IterableStepInterface
{
    /** @var CsvExtractor */
    private $extractor;

    /** @var \Generator */
    private $iterator;

    public function __construct(CsvExtractor $extractor)
    {
        $this->extractor = $extractor;
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['filepath', 'delimiter', 'colums_names', 'skip_first_line']);
        $resolver->setDefault('skip_first_line', true);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        $this->iterator = $this->extractor->extract(
            $state->getOption('filepath'),
            $state->getOption('delimiter'),
            $state->getOption('colums_names'),
            $state->getOption('skip_first_line', true)
        );

        $state->setContext('filepath', $state->getOption('filepath'));
    }

    public function next(ProcessState $state)
    {
        $state->setContext('line_csv', $this->iterator->key());
        $state->setData($this->iterator->current());

        $this->iterator->next();
    }

    public function count(ProcessState $state)
    {
        $file = $state->getOptions()['filepath'];

        return (int) `cat $file | wc -l`;
    }

    public function valid(ProcessState $state)
    {
        return $this->iterator->valid();
    }

    public function getProgress(ProcessState $state)
    {
        return $this->iterator->key();
    }

    public function describe(ProcessState $state)
    {
        $state->info('iterate on each line of {filepath} and transform into array', $state->getOptions());
    }
}
