<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\DependencyInjection\CompilerPass;

use Darkilliant\ImportBundle\Serializer\Symfony\EntityNormalizer;
use Darkilliant\ImportBundle\Persister\DoctrinePersister;
use Darkilliant\ImportBundle\Step\ArrayTargetResolverStep;
use Darkilliant\ImportBundle\Step\DoctrinePersisterStep;
use Darkilliant\ImportBundle\Step\LoadObjectNormalizedStep;
use Darkilliant\ImportBundle\Step\LoadObjectStep;
use Darkilliant\ImportBundle\TargetResolver\ArrayTargetResolver;
use Darkilliant\ImportBundle\TargetResolver\DoctrineTargetResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
class RemoveUnconfigurableStepCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Service depends of doctrine
        if (!$container->hasExtension('doctrine')) {
            $container->removeDefinition(EntityNormalizer::class);

            $container->removeDefinition(DoctrineTargetResolver::class);

            $container->removeDefinition(ArrayTargetResolver::class);
            $container->removeDefinition(ArrayTargetResolverStep::class);

            $container->removeDefinition(DoctrinePersister::class);
            $container->removeDefinition(DoctrinePersisterStep::class);

            $container->removeDefinition(LoadObjectNormalizedStep::class);
            $container->removeDefinition(LoadObjectStep::class);
        }
    }
}
