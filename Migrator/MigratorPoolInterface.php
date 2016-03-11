<?php

namespace Nz\MigrationBundle\Migrator;

use Nz\WordpressBundle\Entity\Post;
use Nz\WordpressBundle\Entity\User;

/**
 * Description of MigratorPool
 *
 * @author tino
 */
interface MigratorPoolInterface
{

    public function addMigrator(MigratorInterface $migrator);

    public function getMigrators();

    public function getMigratorForSrc($src);

}
