<?php

namespace Nz\MigrationBundle\Migrator\Wp;

use Nz\WordpressBundle\Entity\Post;
use Nz\MigrationBundle\Model\Traits\MetaTrait;

/**
 * Description of DefaultPostMigrator
 *
 * @author tino
 */
class DefaultPostMigrator extends BasePostMigrator
{

    protected $config = array();
    protected $post = null;

    public function isSrcMigrator($src)
    {

        if (!$src instanceof Post) {
            return false;
        }

        if (!in_array($src->getType(), array_keys($this->config))) {
            return false;
        }

        $this->src = $src;

        return true;
    }

    public function setUpTarget()
    {
        $this->target = new $this->config[$this->src->getType()]['target_entity']();
    }

    /**
     * Migrate src
     */
    public function migrateSrc($src)
    {
        $fields = $this->config[$this->src->getType()]['fields'];
        foreach ($fields as $setter => $config) {

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
        ;
    }

    /**
     * Migrate wp post metas
     */
    public function migrateMetas(array $metas = array())
    {

        $fieldsConfig = $this->config[$this->src->getType()]['metas'];

        $this->migrateMetasConfig($metas, $fieldsConfig);

        if (in_array(MetaTrait::class, class_uses($this->target))) {

            $fields = $this->config[$this->src->getType()]['extra'];
            $this->migrateExtrasConfig($metas, $fields);
        }
    }
}
