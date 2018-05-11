<?php

declare(strict_types=1);

namespace Darkilliant\ImportBundle;

use Darkilliant\ImportBundle\DependencyInjection\CompilerPass\RegisterImportSerializerCompilerPass;
use Darkilliant\ImportBundle\DependencyInjection\CompilerPass\RemoveUnconfigurableStepCompilerPass;
use Darkilliant\ImportBundle\DependencyInjection\CompilerPass\TransformerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Darkilliant\ImportBundle\DependencyInjection\DarkilliantImportExtension;

/**
 * @codeCoverageIgnore
 * Class DarkilliantImportBundle
 */
class DarkilliantImportBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new DarkilliantImportExtension();
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TransformerCompilerPass());
        $container->addCompilerPass(new RemoveUnconfigurableStepCompilerPass());
        $container->addCompilerPass(new RegisterImportSerializerCompilerPass());
    }
}
