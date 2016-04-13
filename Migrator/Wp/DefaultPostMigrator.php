<?php

namespace Nz\MigrationBundle\Migrator\Wp;

use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Description of DefaultPostMigrator
 *
 * @author tino
 */
class DefaultPostMigrator extends BasePostMigrator
{

    public function isSrcMigrator($src)
    {
        if (
            !parent::isSrcMigrator($src) ||
            !in_array($src->getType(), array_keys($this->config)) ||
            'publish' !== $src->getStatus()
        ) {
            throw new \Exception(sprintf('Not migrator for source class: %s (type %s, status %s)', get_class($src), $src->getType(), $src->getStatus()));
        }

        $this->src = $src;
        return true;
    }

    public function setUpTarget()
    {
        if (!class_exists($this->config[$this->src->getType()]['target_entity'])) {
            throw new \Exception(sprintf('Target class "%s" does not exist', $this->config[$this->src->getType()]['target_entity']));
        }

        $this->target = new $this->config[$this->src->getType()]['target_entity'];
    }

    /**
     * Migrate src
     */
    public function migrate($src)
    {
        $this->setUpTarget();

        $this->migrateFields($src, $this->config[$this->src->getType()]['fields']);
        $this->migrateMetas($src, $this->config[$this->src->getType()]['metas']);
        $this->migrateExtras($src, $this->config[$this->src->getType()]['extra']);

        return $this->target;
    }
}
