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
        if (
            !$src instanceof Post
        ) {
            throw new \Exception(sprintf('Not migrator for source class: %s', get_class($src)));
            /* throw new \Exception(sprintf('Not migrator for source class: %s (status: %s)', get_class($src), $src->getStatus())); */
        }
        return true;
    }
}
