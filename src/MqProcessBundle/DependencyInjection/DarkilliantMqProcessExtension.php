<?php

declare(strict_types=1);

namespace Darkilliant\MqProcessBundle\DependencyInjection;

use PhpAmqpLib\Connection\AMQPLazyConnection;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DarkilliantMqProcessExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $yamlLoader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $yamlLoader->load('services.yml');

        $connectionDefinition = new Definition(AMQPLazyConnection::class);
        $connectionDefinition->setArgument('$host', $mergedConfig['client']['host']);
        $connectionDefinition->setArgument('$port', $mergedConfig['client']['port']);
        $connectionDefinition->setArgument('$user', $mergedConfig['client']['user']);
        $connectionDefinition->setArgument('$password', $mergedConfig['client']['password']);
        $connectionDefinition->setArgument('$vhost', $mergedConfig['client']['vhost']);

        $container->setDefinition('darkilliant_mqprocess_connection', $connectionDefinition);
    }
}
