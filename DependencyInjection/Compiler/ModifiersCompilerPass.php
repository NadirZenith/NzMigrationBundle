<?php

namespace Nz\MigrationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class ModifiersCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('nz.modifier.pool')) {
            return;
        }

        $definition = $container->getDefinition('nz.modifier.pool');

        foreach ($container->findTaggedServiceIds('nz.modifier') as $id => $attributes) {

            $definition->addMethodCall('addModifier', array(new Reference($id), $attributes[0]['type']));
        }
    }
}
