<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class DatetimeModifier implements ModifierInterface
{


    public function modify($value, array $options = array())
    {
        if ($value instanceof \DateTime) {

            return $value;
        }


        $dt = null;

        if (((string) (int) $value === $value) && ($value <= PHP_INT_MAX) && ($value >= ~PHP_INT_MAX)) {
            /* if (($value === $value) && ($value <= PHP_INT_MAX) && ($value >= ~PHP_INT_MAX)) { */
            $dt = new \DateTime();
            $dt->setTimestamp($value);
        } else {

            try {
                $dt = new \DateTime($value);
            } catch (\Exception $ex) {

                if (isset($options['default'])) {
                    return $options['default'];
                }
            }
        }

        return $dt;
    }
}
