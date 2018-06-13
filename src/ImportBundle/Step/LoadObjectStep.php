<?php

namespace Darkilliant\ImportBundle\Step;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Darkilliant\ImportBundle\Loader\ObjectLoader;
use Darkilliant\ImportBundle\TargetResolver\DoctrineTargetResolver;
use Darkilliant\ProcessBundle\State\ProcessState;
use Darkilliant\ProcessBundle\Step\AbstractConfigurableStep;

class LoadObjectStep extends AbstractConfigurableStep
{
    /** @var ObjectLoader */
    private $loader;

    /** @var DoctrineTargetResolver */
    private $resolver;

    public function __construct(ObjectLoader $loader, DoctrineTargetResolver $resolver)
    {
        $this->loader = $loader;
        $this->resolver = $resolver;
    }

    public function configureOptionResolver(OptionsResolver $resolver): OptionsResolver
    {
        $resolver
            ->setRequired(['target_mapping', 'target_resolver']);

        return parent::configureOptionResolver($resolver);
    }

    /**
     * @param ProcessState $state
     *
     * @throws \Exception
     */
    public function execute(ProcessState $state)
    {
        $object = $this->loader->load(
            $this->resolver->resolve(
                $state->getOptions()['target_resolver']
            ),
            $state->getData(),
            $state->getOptions()['target_mapping']
        );

        $state->setContext('class', get_class($object));
        $state->setContext('id', $object->getId());

        $state->info('create object');

        $state->setData($object);
    }

    public function describe(ProcessState $state)
    {
        $state->info('create object {class} with array data', [
            'class' => $state->getOptions()['target_resolver']['entityClass'] ?? '',
        ]);
    }

    public static function isDeprecated(): bool
    {
        return true;
    }
}
