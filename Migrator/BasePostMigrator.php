<?php

namespace Nz\MigrationBundle\Migrator;

use Nz\WordpressBundle\Entity\Post;

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
