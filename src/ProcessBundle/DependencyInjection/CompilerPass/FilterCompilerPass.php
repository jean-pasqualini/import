<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\DependencyInjection\CompilerPass;

use Darkilliant\ProcessBundle\Registry\FilterRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
class FilterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $registryDefinition = $container->findDefinition(FilterRegistry::class);
        $taggedServices = $container->findTaggedServiceIds('darkilliant_process_filter');

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
