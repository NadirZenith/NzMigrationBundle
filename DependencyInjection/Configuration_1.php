<?php

namespace Nz\MigrationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nz_wp_migration');

        $rootNode
            ->children()
                ->arrayNode('user')
                    ->children()
                        ->scalarNode('service_id')->end()
                        ->scalarNode('migration_entity')->end()
                        ->arrayNode('fields')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode(0)->isRequired()->end()
                                    ->scalarNode(1)->defaultValue('string')->end()
                                    ->variableNode(2)
                                        ->beforeNormalization()
                                            ->ifArray()
                                            ->then(function ($a) {
                                                $o = [];
                                                foreach ($a as $key => $value) {
                                                    foreach ($value as $k => $v) {
                                                        $o[$k] = $v;
                                                    }
                                                }
                                                return $o;
                                            })
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('metas')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode(0)->isRequired()->end()
                                    ->scalarNode(1)->defaultValue('string')->end()
                                    ->variableNode(2)
                                        ->beforeNormalization()
                                            ->ifArray()
                                            ->then(function ($a) {
                                                $o = [];
                                                foreach ($a as $key => $value) {
                                                    foreach ($value as $k => $v) {
                                                        $o[$k] = $v;
                                                    }
                                                }
                                                return $o;
                                            })
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                                                
                ->arrayNode('posts')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('service_id')->end()
                            ->scalarNode('migration_entity')->end()
                            ->arrayNode('fields')
                                ->prototype('array')
                                    ->beforeNormalization()
                                        ->ifString()
                                        ->then(function ($v) {

                                            return [0 => $v];
                                        })
                                     ->end()   
                                    ->children()
                                        ->scalarNode(0)->isRequired()->end()
                                        ->scalarNode(1)->defaultValue('string')->end()
                                        ->variableNode(2)
                                            ->beforeNormalization()
                                                ->ifArray()
                                                ->then(function ($a) {
                                                    $o = [];
                                                    foreach ($a as $key => $value) {
                                                        foreach ($value as $k => $v) {
                                                            $o[$k] = $v;
                                                        }
                                                    }
                                                    return $o;
                                                })
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('metas')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode(0)->isRequired()->end()
                                        ->scalarNode(1)->defaultValue('string')->end()
                                        ->variableNode(2)
                                            ->beforeNormalization()
                                                ->ifArray()
                                                ->then(function ($a) {
                                                    $o = [];
                                                    foreach ($a as $key => $value) {
                                                        foreach ($value as $k => $v) {
                                                            $o[$k] = $v;
                                                        }
                                                    }
                                                    return $o;
                                                })
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
                

        return $treeBuilder;
    }
}
