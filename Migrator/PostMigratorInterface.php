<?php

namespace Nz\MigrationBundle\Migrator;

use Nz\WordpressBundle\Entity\Post;

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
