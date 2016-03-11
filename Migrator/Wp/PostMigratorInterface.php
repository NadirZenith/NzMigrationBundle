<?php

namespace Nz\MigrationBundle\Migrator\Wp;

use Nz\WordpressBundle\Entity\Post;
use Nz\MigrationBundle\Migrator\MigratorInterface;

/**
 *
 * @author tino
 */
interface PostMigratorInterface extends MigratorInterface
{

    /**
     * Migrate user
     *
     * @return void
     */
    public function migratePost(Post $post);

    /**
     * Migrate metas
     *
     * @return void
     */
    public function migrateMetas(array $metas = array());
}
