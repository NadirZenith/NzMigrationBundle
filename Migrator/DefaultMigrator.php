<?php

namespace Nz\MigrationBundle\Migrator;

use Nz\MigrationBundle\Model\Traits\MetaTrait;

/**
 * DefaultMigrator
 *
 * @author tino
 */
class DefaultMigrator extends BaseMigrator
{

    protected $src;

    public function isSrcMigrator($src)
    {
        if (!$this->getMigrationConfig($src)) {
            return false;
        }

        $this->src = $src;
        return true;
    }

    public function setUpTarget()
    {
        $config = $this->getMigrationConfig($this->src);

        if (!class_exists($config['target_entity'])) {
            throw new \Exception(sprintf('Target class %s does not exist', $config['target_entity']));
        }

        $this->target = new $config['target_entity'];

        return $config;
    }

    /**
     * Migrate src
     */
    public function migrate($src)
    {
        $config = $this->setUpTarget();

        $this->migrateFields($src, $config['fields']);
        $this->migrateExtras($src, $config['extra']);

        return $this->target;
    }

    private function getMigrationConfig($src)
    {

        foreach ($this->config as $config) {
            if ($src instanceof $config['src_entity']) {
                return $config;
            }
        }

        return false;
    }
}
