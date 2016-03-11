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
        /* d($value); */
        if (is_bool($value) || is_int($value)) {

            return (bool) $value;
        }
        if (in_array($value, self::$enabled_strings)) {
            return true;
        }

        if (in_array($value, self::$disabled_strings)) {
            return false;
        }
        if (isset($options['default'])) {
            return (bool) $options['default'];
        }
        /*
         */
        /*
         */

        throw new \RuntimeException(sprintf('It was not possible to modify the value %s', $value));
    }
}
