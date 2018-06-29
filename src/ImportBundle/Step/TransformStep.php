<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\Step;

use Darkilliant\ImportBundle\Registry\TransformerRegistry;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class TransformStep extends AbstractConfigurableStep
{
    /** @var PropertyAccessor */
    private $accessor;

    /** @var TransformerRegistry */
    private $registry;

    public function __construct(PropertyAccessor $accessor, TransformerRegistry $registry)
    {
        $this->accessor = $accessor;
        $this->registry = $registry;
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired('transforms');

        return parent::configureOptionResolver($resolver);
    }

    /**
     * @throws \TypeError
     */
    public function execute(ProcessState $state)
    {
        $transforms = $state->getOptions()['transforms'];
        $data = $state->getData();

        foreach ($transforms as $transformConfig) {
            if ($this->accessor->isWritable($data, $transformConfig['target'])) {
                $transformer = $this->registry->get($transformConfig['type']);

                $originalValue = $transformConfig['source'] ?? null;

                $transformer->validate($originalValue, $transformConfig['descrition'] ?? $transformConfig['target'], $transformConfig['options'] ?? []);
                $finalValue = $transformer->transform($originalValue, $transformConfig['descrition'] ?? $transformConfig['target'], $transformConfig['options'] ?? []);

                $this->accessor->setValue($data, $transformConfig['target'], $finalValue);
            }
        }

        $state->setData($data);
    }
}
