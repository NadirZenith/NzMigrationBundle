<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class ValueModifier implements ModifierInterface
{

    public function modify($value, array $options = array())
    {
        if (!isset($options['value'])) {
            return null;
        }
        return $options['value'];
    }
}
