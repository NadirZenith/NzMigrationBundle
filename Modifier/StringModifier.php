<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class StringModifier implements ModifierInterface
{

    public function modify($value, array $options = array())
    {
        if (empty($value) && isset($options['string'])) {
            return $options['string'];
        }

        return $value;
    }
}
