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
        /* $pool->removeModifier('stack'); */
        $this->pool = $pool;
    }

    public function modifyStack($value, $modifier, $options)
    {

        return $this->pool->getModifier($modifier)->modify($value, $options);
    }

    public function modify($value, array $options = array())
    {
        /* print_r($value, TRUE); */
        foreach ($options as $K => $stack) {

            $modifier = $stack[0];
            $opt = isset($stack[1]) ? $stack[1] : [];
            /* $mod = $this->pool->getModifier($modifier); */
            /* $value = $mod->modify($value, $opt); */

            $value = $this->modifyStack($value, $modifier, $opt);
        }

        return $value;
    }
}
