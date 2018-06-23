<?php

namespace Darkilliant\ImportBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @internal
 * @codeCoverageIgnore
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $root = $treeBuilder->root('darkilliant_import');

        $root
            ->children()
                ->variableNode('fields_entity_resolver')->defaultValue([])->end()
                ->variableNode('entity_resolver_cache')->defaultValue([])->end()
                ->variableNode('imports')->defaultValue([])->end()
            ->end();

        return $treeBuilder;
    }
}
