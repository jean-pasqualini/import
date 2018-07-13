<?php

namespace Darkilliant\ProcessBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterAliasPublicLoggerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('darkilliant_process');

        foreach ($config['process'] as $processName => $process) {
            $aliasLogger = new Alias($process['logger'] ?? 'logger', true);
            $container->setAlias(sprintf('darkilliant_process_logger_%s', $processName), $aliasLogger);
            $config['process'][$processName]['logger'] = sprintf('darkilliant_process_logger_%s', $processName);
        }

        $container->setParameter('darkilliant_process', $config);
    }
}
