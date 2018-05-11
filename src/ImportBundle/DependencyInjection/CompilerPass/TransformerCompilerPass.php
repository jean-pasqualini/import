<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle\DependencyInjection\CompilerPass;

use Darkilliant\ImportBundle\Registry\TransformerRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
class TransformerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $registryDefinition = $container->findDefinition(TransformerRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('darkilliant_import_transformer');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $registryDefinition->addMethodCall('add', [
                    $attributes['alias'],
                    new Reference($id),
                ]);
            }
        }
    }
}
