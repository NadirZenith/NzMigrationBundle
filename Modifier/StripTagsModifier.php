<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class StripTagsModifier implements ModifierInterface
{

    public function modify($value, array $options = array())
    {
        $options = $this->normalizeOptions($options);

        $value = strip_tags($value, $options['allowable_tags']);
        return $value;
    }

    public function normalizeOptions($options)
    {

        return $this->options = array_merge(array(
            'allowable_tags' => '<p><a><br>'
            ), $options);
    }
}
