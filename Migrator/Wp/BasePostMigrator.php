<?php

namespace Nz\MigrationBundle\Migrator\Wp;

use Nz\WordpressBundle\Entity\Post;
use Nz\MigrationBundle\Migrator\BaseMigrator;

/**
 * Description of BaseUserMigrator
 *
 * @author tino
 */
abstract class BasePostMigrator extends BaseMigrator implements PostMigratorInterface
{

    public function isSrcMigrator($src)
    {
        return $src instanceof Post;
    }
}
