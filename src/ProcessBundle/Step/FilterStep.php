<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\Step;

use Darkilliant\ProcessBundle\Filter\MappingFilter;
use Darkilliant\ProcessBundle\State\ProcessState;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterStep extends AbstractConfigurableStep
{
    /** @var MappingFilter */
    private $mappingFilter;

    public function __construct(MappingFilter $mappingFilter)
    {
        $this->mappingFilter = $mappingFilter;
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['filters']);

        return parent::configureOptionResolver($resolver);
    }

    public function execute(ProcessState $state)
    {
        $isAccept = $this->mappingFilter->isAccept($state->getOptions()['filters']);

        if (!$isAccept) {
            $state->debug('skip current iteration', ['data' => $state->getData()]);
            $state->markIgnore();

            return;
        }
    }
}
