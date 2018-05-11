<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Step;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ImportBundle\Extractor\ExcelSplitter;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;
use Darkilliant\ProcessBundle\Step\IterableStepInterface;

class SplitExcelStep extends AbstractConfigurableStep implements IterableStepInterface
{
    /** @var \ArrayIterator */
    private $iterator;

    /** @var ExcelSplitter */
    private $excelSpliter;

    public function __construct(ExcelSplitter $splitter)
    {
        $this->excelSpliter = $splitter;
    }

    /**
     * @param ProcessState $state
     *
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function execute(ProcessState $state)
    {
        $this->iterator = new \ArrayIterator(
            $this->excelSpliter->split($state->getOptions()['filepath'])
        );
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired('filepath');

        return parent::configureOptionResolver($resolver);
    }

    public function next(ProcessState $state)
    {
        $state->setData($this->iterator->current());

        $this->iterator->next();
    }

    public function valid(ProcessState $state)
    {
        return $this->iterator->valid();
    }

    public function describe(ProcessState $state)
    {
        $state->info(
            'split excel file {filepath} into one csv file for each tab',
            ['filepath' => $state->getOptions()['filepath'] ?? 'unkow']
        );
    }
}
