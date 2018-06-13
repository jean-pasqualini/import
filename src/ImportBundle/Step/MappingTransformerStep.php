<?php

namespace Darkilliant\ImportBundle\Step;

use Darkilliant\ImportBundle\Exception\TransformationException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ImportBundle\Transformer\MappingTransformer;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;

class MappingTransformerStep extends AbstractConfigurableStep
{
    /** @var MappingTransformer */
    private $transformer;

    public function __construct(MappingTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function execute(ProcessState $state)
    {
        try {
            $state->setData($this->transformer->transform([], $state->getOptions()['mapping']));
        } catch (TransformationException $exception) {
            $state->error($exception->getMessage());
            $state->markFail();
        }
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setRequired(['mapping']);

        return parent::configureOptionResolver($resolver);
    }

    public static function isDeprecated(): bool
    {
        return true;
    }
}
