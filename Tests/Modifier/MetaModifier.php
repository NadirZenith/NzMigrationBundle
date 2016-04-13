<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class MetaModifier implements ModifierInterface
{

    public function modify($value, array $options = array())
    {
        return [$options['key'], $value];
        
    }
}
