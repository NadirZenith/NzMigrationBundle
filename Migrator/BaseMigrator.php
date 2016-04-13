<?php

namespace Nz\MigrationBundle\Migrator;

use Nz\MigrationBundle\Model\Traits\MetaTrait;
use Nz\MigrationBundle\Modifier\ModifierPoolInterface;

/**
 * Description of BaseMigrator
 *
 * @author tino
 */
abstract class BaseMigrator implements MigratorInterface
{

    protected $modifiersPool;
    protected $target;
    protected $config;

    public function setUpTarget()
    {
        if (!class_exists($this->class)) {
            throw new \Exception(sprintf('Target class %s does not exist', $this->class));
        }

        $this->target = new $this->class;
    }

    protected function migrateFields($src, array $fields = array())
    {
        #d($src);
        foreach ($fields as $setter => $config) {
            list($get, $modifier, $options) = $this->fixConfig($config);

            if (false === strpos($get, '%')) {
                //Source getter
                $getter = sprintf('get%s', ucfirst($get)); //getter
                if (!is_callable(array($src, $getter))) {
                    throw new \Exception(sprintf('Source class %s has no method %s', get_class($src), $getter));
                }
                $value = $src->$getter();
            }else{
                //Target getter
                $getter = sprintf('get%s', ucfirst(trim($get, '%'))); //getter
                if (!is_callable(array($this->target, $getter))) {
                    throw new \Exception(sprintf('Target class %s has no method %s', get_class($this->target), $getter));
                }
                $value = $this->target->$getter();
            }

            $final_value = $this->modifyValue($value, $modifier, $options);

            $setter = sprintf('set%s', ucfirst($setter));
            if (!is_callable(array($this->target, $setter))) {
                throw new \Exception(sprintf('Target class %s has no method %s', get_class($this->target), $setter));
            }
            $this->target->$setter($final_value);
        }
        
        #dd($this->target);
    }

    /**
     * Migrate to target metas
     */
    protected function migrateExtras($src, array $fields = array())
    {
        if (!in_array(MetaTrait::class, class_uses($this->target))) {
            return;
        }

        foreach ($fields as $key => $config) {
            list($meta_key, $modifier, $options) = $this->fixConfig($config);

            foreach ($src->getMetas()->toArray() as $meta) {
                if ($meta_key !== $meta->getKey()) {
                    continue;
                }

                $final_value = $this->modifyValue($meta->getValue(), $modifier, $options);
                $this->target->setMeta($key, $final_value);
            }
        }
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setConfig(array $config = array())
    {
        $this->config = $config;
    }

    public function setModifierPool(ModifierPoolInterface $modifierPool)
    {
        $this->modifiersPool = $modifierPool;
    }

    protected function modifyValue($value, $modifier = 'string', array $options = array())
    {
        $mod = $this->modifiersPool->getModifier($modifier);

        if (!$mod) {
            throw new \Exception(sprintf('Modifier "%s" does not exist', $modifier));
        }

        return $mod->modify($value, $options);
    }

    protected function fixConfig(array $config = array())
    {

        $conf [] = $config[0][0]; //key
        if (1 === count($config)) {
            $conf[] = $config[0][1]; //modifier
            $conf[] = $config[0][2]; //options
        } else {
            $conf[] = 'stack'; //modifier
            $conf[] = $config; //options
        }

        return $conf;
    }
}
