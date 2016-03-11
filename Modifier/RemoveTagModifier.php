<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class RemoveTagModifier implements ModifierInterface
{

    public function modify($value, array $options = array())
    {

        $allowable_tags = isset($options['allowable_tags']) ? $options['allowable_tags'] : '';




        $value = strip_tags($value, $allowable_tags);

        return $value;
    }
}
