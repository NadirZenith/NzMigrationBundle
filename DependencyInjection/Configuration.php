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

        $this->addUserSection($rootNode);
        $this->addPostsSection($rootNode);
        
        return $treeBuilder;
        
    }
    
    /**
     *  Add user config section
     */
    private function addUserSection($rootNode){
        $rootNode
            ->children()
                ->arrayNode('user')
                    ->children()
                        ->scalarNode('service_id')->info('User migration handler service id')->cannotBeEmpty()->defaultValue('nz.wp_migration.user_migrator_default')->end()
                        ->scalarNode('src_entity')->info('Src Entity')->cannotBeEmpty()->defaultValue('\Nz\WordpressBundle\Entity\User')->end()
                        ->scalarNode('target_entity')->info('Migration Entity')->cannotBeEmpty()->end()
                        ->append($this->addFieldsMappingNode('fields'))
                        ->append($this->addFieldsMappingNode('metas'))
                        ->append($this->addFieldsMappingNode('extra'))
                    ->end()
                ->end()
            ->end()
        ;
    }
    
    /**
     *  Add posts config section
     */
    private function addPostsSection($rootNode){
        $rootNode
            ->children()
                ->arrayNode('posts')
                    ->beforeNormalization()
                        ->ifArray()
                            ->then(function ($a) {
                                $n = [];
                                foreach ($a as $k => $v){
                                    $n[ltrim($k, '_')] = $v;
                                }

                                return $n;
                            })
                        ->end()
                        ->prototype('array')
                            ->children()
                                ->scalarNode('service_id')->info('Post migration handler service id')->cannotBeEmpty()->defaultValue('nz.wp_migration.post_migrator_default')->end()
                                ->scalarNode('src_entity')->info('Src Entity')->cannotBeEmpty()->defaultValue('\Nz\WordpressBundle\Entity\Post')->end()
                                ->scalarNode('target_entity')->info('Target Entity')->cannotBeEmpty()->end()
                                ->append($this->addFieldsMappingNode('fields'))
                                ->append($this->addFieldsMappingNode('metas'))
                                ->append($this->addFieldsMappingNode('extra'))

                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
    
    /**
     * @param string $field_type fields or metas
     */
    private function addFieldsMappingNode($field_type ){
        $builder = new TreeBuilder();
        $node = $builder->root($field_type);
        
        $node
            ->prototype('array')
                ->beforeNormalization()
                    ->ifString()->then(function ($v) {return [0 => $v];})
                ->end()
                ->children()
                    ->scalarNode(0)->cannotBeEmpty()->end()
                    ->scalarNode(1)->cannotBeEmpty()->defaultValue('string')->end()
                    ->variableNode(2)->cannotBeEmpty()->defaultValue([])
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
        ;
                            
        return $node;

    }
    
}
