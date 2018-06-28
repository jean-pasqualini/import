<?php

declare(strict_types=1);

namespace Darkilliant\ProcessBundle\DependencyInjection\CompilerPass;

use Darkilliant\ProcessBundle\ProcessNotifier\ChainProcessNotifier;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ProcessNotifierCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $registryDefinition = $container->findDefinition(ChainProcessNotifier::class);
        $taggedServices = $container->findTaggedServiceIds('darkilliant_process_notifier');

        foreach ($taggedServices as $id => $tags) {
            $registryDefinition->addMethodCall('add', [
                new Reference($id),
            ]);
        }
    }
}
