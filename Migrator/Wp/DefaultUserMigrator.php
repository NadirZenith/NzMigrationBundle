<?php

namespace Nz\MigrationBundle\Migrator\Wp;

use Nz\WordpressBundle\Entity\User;
use Nz\MigrationBundle\Model\Traits\MetaTrait;

/**
 * Description of DefaultUserMigrator
 *
 * @author tino
 */
class DefaultUserMigrator extends BaseUserMigrator
{

    public function setUpTarget()
    {
        if (!class_exists($this->config['target_entity'])) {
            throw new \Exception(sprintf('Target class "%s" does not exist', $this->config['target_entity']));
        }

        $this->target = new $this->config['target_entity'];
    }

    public function migrate($src)
    {
        $this->setUpTarget();
        $this->migrateFields($src, $this->config['fields']);
        $this->migrateMetas($src, $this->config['metas']);
        $this->migrateExtras($src, $this->config['extra']);

        return $this->target;
    }
}
