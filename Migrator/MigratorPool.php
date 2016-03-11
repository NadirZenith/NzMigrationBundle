<?php

namespace Nz\MigrationBundle\Migrator;

use Nz\WordpressBundle\Entity\Post;
use Nz\WordpressBundle\Entity\User;

/**
 * Description of MigratorPool
 *
 * @author tino
 */
class MigratorPool implements MigratorPoolInterface
{

   
    protected $pool = array();

    public function addMigrator(MigratorInterface $migrator)
    {
        $this->pool[] = $migrator;
    }

    public function getMigrators()
    {
        return $this->pool;
    }

    public function getMigratorForSrc($src)
    {
        foreach ($this->pool as $migrator) {
            if ($migrator->isSrcMigrator($src)) {
                return $migrator;
            }
        }

        return FALSE;
    }
}
