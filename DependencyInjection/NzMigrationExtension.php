<?php

namespace Nz\MigrationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NzMigrationExtension extends Extension
{

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $bundles = $container->getParameter('kernel.bundles');
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('core.xml');

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.xml');
        }

        $loader->load('migrators.xml');
        $loader->load('modifiers.xml');

        $this->configureDefaultMigrators($config, $container);
    }

    protected function configureDefaultMigrators($config, ContainerBuilder $container)
    {
        if ($config['wp']) {
            $config = $config['wp'];

            //handler
            $handler_id = 'nz.migration.handler.wp';
            $definition = $container->getDefinition($handler_id);
            $definition->addMethodCall('setConfig', [$config]);
            $container->setDefinition($handler_id, $definition);

            //user
            $user_migrator_id = $config['user']['service_id'];
            $definition = $container->getDefinition($user_migrator_id);
            $definition->replaceArgument(0, $config['user']['target_entity']);
            $definition->addMethodCall('setConfig', [$config['user']]);
            $container->setDefinition($user_migrator_id, $definition);

            //posts
            $post_migrator_id = 'nz.migration.wp.post_default';
            $definition = $container->getDefinition($post_migrator_id);
            $definition->addMethodCall('setConfig', [$config['posts']]);
            $container->setDefinition($post_migrator_id, $definition);
        }
    }
}
