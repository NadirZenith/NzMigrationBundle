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
            $match = $src->getMetas()->filter(function($meta)use ($meta_key) {
                if ($meta_key === $meta->getKey()) {
                    return $meta;
                }
            });

            if ($match->isEmpty()) {
                if (isset($options['required']) && $options['required']) {

                    throw new \Exception(sprintf('Source "%s" has no meta named "%s"', get_class($src), $meta_key));
                }

                continue;
            }

            $setter = sprintf('set%s', ucfirst($setterKey));
            if (!is_callable(array($this->target, $setter))) {
                throw new \Exception(sprintf('Target class "%s" has no method "%s"', get_class($this->target), $setter));
            }

            $final_value = $this->modifyValue($match->first()->getValue(), $modifier, $options);
            $this->target->$setter($final_value);
        }
    }
}
