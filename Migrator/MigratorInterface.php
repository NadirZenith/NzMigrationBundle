<?php

namespace Nz\MigrationBundle\Migrator;

use Nz\MigrationBundle\Modifier\ModifierPoolInterface;

/**
 *
 * @author tino
 */
interface MigratorInterface
{

    public function __construct($class);

    /**
     * Set up target
     *
     * @return void
     */
    public function setUpTarget();

    /**
     * Get migrated entity
     *
     * @return object
     */
    public function getTarget();

    /**
     * Migrate Src
     *
     * @return object
     */
    public function migrateSrc($src);

    /**
     * Get entity class name
     *
     * @return string
     */
    public function getClass();


    /**
     * Is migrator compatible with object.
     *
     * @return boolean
     */
    public function isSrcMigrator($src);

    /**
     * Set Pool of Modifiers.
     *
     * @return void
     */
    public function setModifierPool(ModifierPoolInterface $modifierPool);
}
