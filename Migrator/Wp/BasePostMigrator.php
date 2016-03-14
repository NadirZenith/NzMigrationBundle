<?php

namespace Nz\MigrationBundle\Migrator\Wp;

use Nz\WordpressBundle\Entity\Post;

/**
 * Description of BaseUserMigrator
 *
 * @author tino
 */
abstract class BasePostMigrator extends BaseWpMigrator
{

    public function isSrcMigrator($src)
    {
        return $src instanceof Post;
    }
}
