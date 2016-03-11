<?php

namespace Nz\MigrationBundle\Migrator\Wp;

use Nz\WordpressBundle\Entity\Post;
use Nz\MigrationBundle\Modifier\ModifierPoolInterface;

/**
 * Description of DefaultPostMigrator
 *
 * @author tino
 */
class DefaultPostMigrator extends BasePostMigrator
{

    protected $config = array();
    protected $post = null;

    public function setUpEntity()
    {
        /* $this->target = new $this->class; */
        $this->target = new $this->config[$this->src->getType()]['target_entity'];
    }

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

    public function migratePost(Post $post)
    {

        $this->migrateEntity($post);
    }

    public function migrateSrc($src)
    {
        /* dd($src); */
        $fields = $this->config[$this->src->getType()]['fields'];
        foreach ($fields as $setter => $config) {
            $getter = sprintf('get%s', ucfirst($config[0]));
            $setter = sprintf('set%s', ucfirst($setter));
            $value = $src->$getter();
            $final_value = $this->modifyValue($value, $config[1], $config[2]);

            if (!is_null($final_value)) {

                $this->target->$setter($final_value);
            }
        }
        ;
    }

    public function migrateMetas(array $metas = array())
    {
        /* dd($metas); */
        $fields = $this->config[$this->src->getType()]['metas'];
        foreach ($fields as $setter => $config) {
            $meta_key = $config[0];

            if (isset($metas[$meta_key])) {
                $final_value = $this->modifyValue($metas[$meta_key], $config[1], $config[2]);

                $setter = sprintf('set%s', ucfirst($setter));
                /* dd($final_value); */
                if (!is_null($final_value)) {
                    $this->target->$setter($final_value);
                    /* call_user_func_array([ $this->target, $setter], $final_value); */
                } else {
                    continue;
                }
            }
        }

        $this->migrateExtras($metas);
    }

    public function migrateExtras(array $metas = array())
    {
        /* dd($metas); */
        $fields = $this->config[$this->src->getType()]['extra'];
        foreach ($fields as $setter => $config) {
            $meta_key = $config[0];

            if (isset($metas[$meta_key])) {
                $final_value = $this->modifyValue($metas[$meta_key], $config[1], $config[2]);

                if (is_array($final_value)) {

                    $this->target->setMeta($final_value[0], $final_value[1]);
                }
            }
        }
    }

    /**
     * Dependency injection
     */
    public function addPostTypeConfig($type, array $config = array())
    {

        $this->config[$type] = $config;
    }
}
