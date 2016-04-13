<?php

namespace Nz\MigrationBundle\Migrator;

use Nz\MigrationBundle\Modifier\ModifierPoolInterface;

/**
 *
 * @author tino
 */
interface MigratorInterface
{

    /**
     * Get entity class name
     *
     * @return string
     */
    /*public function getClass();*/

    /**
     * Set up target
     *
     * @return void
     */
    /*public function setUpTarget();*/

    /**
     * Get migrated entity
     *
     * @return object
     */
    /*public function getTarget();*/

    /**
     * Is migrator compatible with object.
     *
     * @return boolean
     */
    public function isSrcMigrator($src);

    /**
     * Migrate Src
     *
     * @return object
     */
    public function migrate($src);
}
