<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class PregReplaceModifier implements ModifierInterface
{

    public function modify($value, array $options = array())
    {
        $options = $this->normalizeOptions($options);
        
        $value = preg_replace($options['pattern'], $options['replace'], $value);

        return $value;
    }

    public function normalizeOptions(array $options = array())
    {

        return array_merge(array(
            //shortcodes
            'pattern' => '/(\[.*?\])(?:.+?)(\[\/.*?\])/',
            'replace' => ''
            ), $options);
    }
}
