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
     * Set up migration entity from class.
     *
     * @return void
     */
    public function setUpEntity();

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
     * Get excluded metas.
     *
     * @return array
     */
    public function getExcludedMetasKeysRegex();

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
