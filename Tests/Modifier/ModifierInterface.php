<?php

namespace Nz\MigrationBundle\Modifier;

/**
 *
 * @author tino
 */
interface ModifierInterface
{

    public function modify($value, array $options = array());
}
