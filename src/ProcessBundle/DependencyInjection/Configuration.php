<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 5/8/18
 * Time: 11:36 AM.
 */

namespace Darkilliant\ProcessBundle\DependencyInjection;

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
        $root = $tree->root('darkilliant_process');

        $root
            ->children()
                ->arrayNode('process')
                    ->useAttributeAsKey('name')
                    ->prototype('variable')->end()
                ->end()
            ->end();

        return $tree;
    }
}
