<?php

namespace Nz\MigrationBundle\Migrator\Wp;

use Nz\WordpressBundle\Entity\User;

/**
 *
 * @author tino
 */
interface UserMigratorInterface extends MigratorInterface
{

    /**
     * Migrate user
     *
     * @return void
     */
    public function migrateUser(User $user);

    /**
     * Migrate metas
     *
     * @return void
     */
    public function migrateMetas(array $metas = array());
}
