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

    public function migrateSrc($src)
    {

        foreach ($this->config['fields'] as $setter => $config) {

            $getter = sprintf('get%s', ucfirst($config[0]));
            if (is_callable(array($src, $getter))) {
                $value = $src->$getter();
            }

            $setter = sprintf('set%s', ucfirst($setter));
            if (is_callable(array($this->target, $setter))) {
                $final_value = $this->modifyValue($value, $config[1], $config[2]);

                if (!is_null($final_value)) {

                    $this->target->$setter($final_value);
                }
            }
        }
    }

    /**
     * Migrate wp post metas
     */
    public function migrateMetas(array $metas = array())
    {

        $fieldsConfig = $this->config['metas'];

        $this->migrateMetasConfig($metas, $fieldsConfig);

        if (in_array(MetaTrait::class, class_uses($this->target))) {

            $fields = $this->config['extra'];
            $this->migrateExtrasConfig($metas, $fields);
        }
    }
}
