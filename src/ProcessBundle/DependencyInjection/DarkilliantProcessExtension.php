<?php

namespace Darkilliant\ProcessBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @internal
 */
class DarkilliantProcessExtension extends ConfigurableExtension
{
    const PARAMETER_NAME = 'darkilliant_process';

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $yamlLoader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $yamlLoader->load('services.yml');

        $container->setParameter(self::PARAMETER_NAME, $mergedConfig);
    }
}
