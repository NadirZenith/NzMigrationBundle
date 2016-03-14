<?php

namespace Nz\MigrationBundle\Migrator\Wp;

use Nz\MigrationBundle\Migrator\BaseMigrator;

/**
 * Description of BaseWpMigrator 
 *
 * @author tino
 */
abstract class BaseWpMigrator extends BaseMigrator
{

    protected $config;

    /**
     * Dependency injection
     */
    public function setConfig(array $config = array())
    {

        $this->config = $config;
    }

    /**
     * Migrate to target fields
     */
    protected function migrateMetasConfig(array $metas = array(), array $fieldsConfig = array())
    {
        foreach ($fieldsConfig as $setterKey => $config) {
            $meta_key = $config[0];

            foreach ($metas as $meta) {
                if ($meta_key === $meta->getKey()) {
                    $setter = sprintf('set%s', ucfirst($setterKey));
                    if (is_callable(array($this->target, $setter))) {
                        $final_value = $this->modifyValue($meta->getValue(), $config[1], $config[2]);
                        $this->target->$setter($final_value);
                    }
                }
            }
        }
    }

    /**
     * Migrate to target metas
     */
    protected function migrateExtrasConfig(array $metas = array(), array $fieldsConfig = array())
    {
        foreach ($fieldsConfig as $key => $config) {
            $meta_key = $config[0];
            foreach ($metas as $meta) {
                if ($meta_key === $meta->getKey()) {
                    $final_value = $this->modifyValue($meta->getValue(), $config[1], $config[2]);
                    if (!empty($final_value)) {
                        $this->target->setMeta($key, $final_value);
                    }
                }
            }
        }
    }

    /**
     * Get excluded metas.
     *
     * @return array
     */
    public function getExcludedMetasKeysRegex()
    {
        return array();
    }
}
