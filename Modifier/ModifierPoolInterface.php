<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Modifier pool interface
 *
 * @author tino
 */
interface ModifierPoolInterface
{

    /**
     * Add new modifier to pool
     * 
     * @param ModifierInterface $modifier Modifier
     * @param string $name Modifier name
     */
    public function addModifier(ModifierInterface $modifier, $name);

    /**
     * Get modifiers
     * 
     * @return array Modifiers array
     */
    public function getModifiers();

    /**
     * Get modifier by name
     * 
     * @param string $name Modifier name
     * 
     * @return ModifierInterface Modifier
     */
    public function getModifier($name);
    
    public function removeModifier($name);

}
