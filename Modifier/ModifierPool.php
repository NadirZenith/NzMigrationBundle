<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of MigratorPool
 *
 * @author tino
 */
class ModifierPool implements ModifierPoolInterface
{

    protected $pool = array();

    public function addModifier(ModifierInterface $modifier, $name)
    {
        $this->pool[$name] = $modifier;
    }

    public function getModifiers()
    {
        return $this->pool;
    }

    public function getModifier($name)
    {

        return isset($this->pool[$name]) ? $this->pool[$name] : false;
    }

    public function removeModifier($name)
    {
        unset($this->pool[$name]);

        return $this;
    }
}
