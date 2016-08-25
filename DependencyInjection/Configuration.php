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
        $rootNode = $treeBuilder->root('nz_migration');

        $this->addDefaultSection($rootNode);
        $this->addWpSection($rootNode);
        
        return $treeBuilder;
        
    }
    
    /**
     */
    private function addDefaultSection($rootNode){
        $rootNode
            ->children()
                ->arrayNode('default')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('entity_manager')->info('Wp Entity Manager')->cannotBeEmpty()->defaultValue('default')->end()

                        ->arrayNode('migrations')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('service_id')->info('Post migration handler service id')->cannotBeEmpty()->defaultValue('nz.migration.wp.post_default')->end()
                                        ->scalarNode('src_entity')->info('Src Entity')->cannotBeEmpty()->defaultValue('\Nz\WordpressBundle\Entity\Post')->end()
                                        ->scalarNode('target_entity')->info('Target Entity')->cannotBeEmpty()->end()
                                        ->append($this->addFieldsMappingNode('fields'))
                                        ->append($this->addFieldsMappingNode('extra'))
                                    ->end()
                                ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
    
    /**
     */
    private function addWpSection($rootNode){
        $rootNode
            ->children()
                ->node('wp', 'array')
                    ->children()
            
                            ->scalarNode('entity_manager')->info('Wp Entity Manager')->cannotBeEmpty()->defaultValue('default')->end()
                            ->arrayNode('excluded_types')->info('Excluded migration post types')->defaultValue(['page', 'nav_menu_item', 'revision'])
                                ->prototype('scalar')->end()
                            ->end()
                            /*->arrayNode('excluded_types')->info('Excluded migration post types')->defaultValue(['page', 'nav_menu_item', 'revision'])->prototype('scalar')->end()*/
                            ->arrayNode('user')
                                ->children()
                                    ->scalarNode('service_id')->info('User migration handler service id')->cannotBeEmpty()->defaultValue('nz.migration.wp.user_default')->end()
                                    ->scalarNode('src_entity')->info('Src Entity')->cannotBeEmpty()->defaultValue('\Nz\WordpressBundle\Entity\User')->end()
                                    ->scalarNode('target_entity')->info('Migration Entity')->cannotBeEmpty()->end()
                                    ->append($this->addFieldsMappingNode('fields'))
                                    ->append($this->addFieldsMappingNode('metas'))
                                    ->append($this->addFieldsMappingNode('extra'))
                                ->end()
                            ->end()
            
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
                                            ->scalarNode('service_id')->info('Post migration handler service id')->cannotBeEmpty()->defaultValue('nz.migration.wp.post_default')->end()
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
                        ->always(function ($v) {
                            if(is_string($v)){
                                return array($v);
                            }
                            
                            if(is_array($v) && count($v)> 1){
                                if(!is_array($v[0])){
                                    return [$v];
                                }
                            }
                            return $v;
                            
                        })
                    ->end()
                ->prototype('array')
                    ->beforeNormalization()
                        ->ifString()->then(function ($v) {
                            return array($v, 'string');
                        })
                    ->end()
                    ->children()
                        ->scalarNode(0)->cannotBeEmpty()->end()
                        ->scalarNode(1)->cannotBeEmpty()->defaultValue('string')->end()
                        ->variableNode(2)->defaultValue([])
                            ->beforeNormalization()
                                ->ifArray()
                                ->then(function ($a) {
                                    if(empty($a)){
                                        return array();
                                    }
                                    if(count($a[0])===1){
                                        //normal
                                        return $this->fixOptions($a);
                                    }else{
                                        //stack
                                        $result = [];
                                        foreach ($a as $stack ) {
                                            $result[] = [
                                                $stack[0],
                                                $stack[1],
                                                $this->fixOptions($stack[2])
                                            ];
                                        }
                                        return $result;
                                    }

                                })
                            ->end()
                        ->end()
                    ->end()
                ->end()
/*            
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
                                if(count($a[0])===1){
                                    //normal
                                    return $this->fixOptions($a);
                                }else{
                                    //stack
                                    $result = [];
                                    foreach ($a as $stack ) {
                                        $result[] = [
                                            $stack[0],
                                            $stack[1],
                                            $this->fixOptions($stack[2])
                                        ];
                                    }
                                    return $result;
                                }
                               
                            })
                        ->end()
                    ->end()
                ->end()
 */
            ->end()
        ;
                            
        return $node;

    }
    
    private function fixOptions($a)
    {
        $result = [];
        foreach ($a as $option ) {
            $result = array_merge($result,$option);
        }
        
        return $result;

    }
    
}
