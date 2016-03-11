<?php

namespace Nz\MigrationBundle\Migrator\Wp;

use Nz\WordpressBundle\Entity\User;

/**
 * Description of BaseUserMigrator
 *
 * @author tino
 */
abstract class BaseUserMigrator extends BaseMigrator implements UserMigratorInterface
{

    public function isSrcMigrator($src)
    {
        return $src instanceof User;
    }
}
