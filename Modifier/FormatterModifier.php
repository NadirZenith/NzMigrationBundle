<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class FormatterModifier implements ModifierInterface
{

    protected $formatter;

    public function modify($value, array $options = array())
    {
        $options = $this->normalizeOptions($options);

        /*d($value);*/
        $value = $this->formatter->transform($options['transformer'], $value);
        /*dd($value);*/
        return $value;
    }

    public function normalizeOptions($options)
    {
        return array_merge(array(
            'transformer' => 'markdown',
            ), $options);
    }

    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;
    }
}
