<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class StackModifier implements ModifierInterface
{

    protected $pool;

    public function setPool(ModifierPoolInterface $pool)
    {
        $this->pool = $pool;
    }

    public function modifyStack($value, $modifier, $options)
    {
         $mod = $this->pool->getModifier($modifier);

        if (!$mod) {
            throw new \Exception(sprintf('Modifier "%s" does not exist', $modifier));
        }

        return $mod->modify($value, $options);
        
        /*return $this->pool->getModifier($modifier)->modify($value, $options);*/
    }

    public function modify($value, array $options = array())
    {
        foreach ($options as $K => $stack) {
            $modifier = $stack[1];
            $opt = isset($stack[2]) ? $stack[2] : [];

            $value = $this->modifyStack($value, $modifier, $opt);
        }

        return $value;
    }
}
