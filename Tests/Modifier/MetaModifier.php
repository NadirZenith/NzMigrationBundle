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
        dd($value);
        if (empty($value)) {
            if (isset($options['string'])) {
                return $options['string'];
            }
            return null;
        }

        return $value;
    }
}
