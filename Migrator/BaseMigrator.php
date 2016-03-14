<?php

namespace Nz\MigrationBundle\Migrator;

use Nz\MigrationBundle\Modifier\ModifierPoolInterface;

/**
 * Description of BaseMigrator
 *
 * @author tino
 */
abstract class BaseMigrator implements MigratorInterface
{

    protected $modifiersPool;
    protected $class;
    protected $target;

    public function __construct($targetClass)
    {
        $this->class = $targetClass;
    }

    public function setUpTarget()
    {
        $this->target = new $this->class;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setModifierPool(ModifierPoolInterface $modifierPool)
    {
        $this->modifiersPool = $modifierPool;
    }

    protected function modifyValue($value, $modifier = 'string', array $options = array())
    {
        return $this->modifiersPool->getModifier($modifier)->modify($value, $options);
    }
}
