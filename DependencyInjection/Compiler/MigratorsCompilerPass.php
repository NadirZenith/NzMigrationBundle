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
class MigratorsCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('nz.migrator.pool')) {
            return;
        }

        $definition = $container->getDefinition('nz.migrator.pool');

        foreach ($container->findTaggedServiceIds('nz.migrator') as $id => $attributes) {
            $definition->addMethodCall('addMigrator', array(new Reference($id)));
        }
    }
}
