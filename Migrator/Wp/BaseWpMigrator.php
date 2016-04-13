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

    /**
     * Migrate to target fields
     */
    protected function migrateMetas($src, array $fields = array())
    {
        foreach ($fields as $setterKey => $config) {

            list($meta_key, $modifier, $options) = $this->fixConfig($config);
            foreach ($src->getMetas()->toArray() as $meta) {
                if ($meta_key !== $meta->getKey()) {
                    continue;
                }

                $setter = sprintf('set%s', ucfirst($setterKey));
                if (!is_callable(array($this->target, $setter))) {
                    throw new \Exception(sprintf('Target class "%s" has no method "%s"', get_class($this->target), $setter));
                }

                $final_value = $this->modifyValue($meta->getValue(), $modifier, $options);
                $this->target->$setter($final_value);
            }
        }
    }

   
}
