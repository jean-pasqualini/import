<?php

namespace Darkilliant\ImportBundle\Step;

use Darkilliant\ImportBundle\Serializer\Serializer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;

class LoadObjectNormalizedStep extends AbstractConfigurableStep
{
    /** @var Serializer */
    private $denormalizer;

    public function __construct(Serializer $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired(['entity_class', 'serializer']);
        $resolver->setDefault('serializer', 'auto');

        return parent::configureOptionResolver($resolver);
    }

    /**
     * @param ProcessState $state
     *
     * @throws \Exception
     */
    public function execute(ProcessState $state)
    {
        $object = $this->denormalizer->denormalize(
            $state->getData(),
            $state->getOptions()['entity_class'],
            $state->getOptions()['serializer']
        );

        $state->setContext('class', get_class($object));
        $state->setContext('id',
            (method_exists($object, 'getId'))
            ? $object->getId()
            : null
        );

        $state->info('create object');

        $state->setData($object);
    }

    public function describe(ProcessState $state)
    {
        $state->info('create object {class} with array data', [
            'class' => $state->getOptions()['entity_class'] ?? '',
        ]);
    }
}
