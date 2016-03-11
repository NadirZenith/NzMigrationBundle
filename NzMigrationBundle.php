<?php

namespace Nz\MigrationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Nz\MigrationBundle\DependencyInjection\Compiler\MigratorsCompilerPass;
use Nz\MigrationBundle\DependencyInjection\Compiler\ModifiersCompilerPass;

class NzMigrationBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MigratorsCompilerPass());
        $container->addCompilerPass(new ModifiersCompilerPass());
    }
}
