<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class BooleanModifier implements ModifierInterface
{

    
    protected static $enabled_strings = [
        'on',
        'publish',
        'enabled',
        'valid',
        'yes',
        'ok',
        'true',
    ];
    protected static $disabled_strings = [
        'off',
        'disabled',
        'invalid',
        'no',
        'ko',
        'false',
    ];

    public function modify($value, array $options = array())
    {
        if (is_bool($value) || is_int($value)) {

            $value = (bool) $value;
        } else
        if (in_array($value, self::$enabled_strings)) {
            $value = true;
        } else
        if (in_array($value, self::$disabled_strings)) {
            $value = false;
        } else

        if (isset($options['default'])) {
            $value = (bool) $options['default'];
        }

        return $value;
    }
}
