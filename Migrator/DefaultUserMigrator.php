<?php

namespace Nz\MigrationBundle\Migrator;

use Nz\WordpressBundle\Entity\User;
use Nz\MigrationBundle\Modifier\ModifierPoolInterface;
use FOS\UserBundle\Model\User as UserModel;

/**
 * Description of DefaultUserMigrator
 *
 * @author tino
 */
class DefaultUserMigrator extends BaseUserMigrator
{

    protected $migrationFields = array();
    protected $migrationMetas = array();

    public function migrateUser(User $user)
    {
        $this->migrateSrc($user);
    }

    public function migrateSrc($src)
    {
        foreach ($this->migrationFields as $setter => $config) {
            $getter = sprintf('get%s', ucfirst($config[0]));
            $setter = sprintf('set%s', ucfirst($setter));

            $value = $src->$getter();
            $final_value = $this->modifyValue($value, $config[1], $config[2]);
            $this->target->$setter($final_value);
        }
    }

    public function migrateMetas(array $metas = array())
    {

        foreach ($this->migrationMetas as $setter => $config) {
            $meta_key = $config[0];

            if (isset($metas[$meta_key])) {
                $final_value = $this->modifyValue($metas[$meta_key], $config[1], $config[2]);

                $setter = sprintf('set%s', ucfirst($setter));
                $this->target->$setter($final_value);
            }
        }
    }

    /**
     * Dependency injection
     */
    public function setMigrationFields(array $migrationFields = array())
    {
        $this->migrationFields = $migrationFields;
    }

    public function setMigrationMetas(array $migrationMetas = array())
    {
        $this->migrationMetas = $migrationMetas;
    }
}
