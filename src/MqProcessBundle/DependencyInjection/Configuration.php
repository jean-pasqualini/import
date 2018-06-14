<?php

declare(strict_types=1);

namespace Darkilliant\MqProcessBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @internal
 * Class Configuration
 *
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $tree = new TreeBuilder();
        $root = $tree->root('darkilliant_mq_process');

        $root
            ->children()
                ->arrayNode('client')
                    ->children()
                        ->scalarNode('type')->defaultValue('amqp_lib')->end()
                        ->scalarNode('host')->isRequired()->end()
                        ->scalarNode('port')->isRequired()->end()
                        ->scalarNode('user')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                        ->scalarNode('vhost')->isRequired()->end()
                    ->end()
                ->end()
            ->end();

        return $tree;
    }
}
