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
        if (empty($value)) {
            if (isset($options['string'])) {
                return $options['string'];
            }
            return null;
        }

        return $value;
    }
}
