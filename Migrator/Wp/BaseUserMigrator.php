<?php

namespace Nz\MigrationBundle\Migrator\Wp;

use Nz\WordpressBundle\Entity\User;

/**
 * Description of BaseUserMigrator
 *
 * @author tino
 */
abstract class BaseUserMigrator extends BaseWpMigrator
{

    public function isSrcMigrator($src)
    {
        if (!$src instanceof User) {
            return false;
        }

        return true;
    }
}
