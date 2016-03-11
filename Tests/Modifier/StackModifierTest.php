<?php

namespace Nz\MigrationBundle\Tests\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class StackModifierTest extends \PHPUnit_Framework_TestCase
{

    protected $pool;

    public function addPool(ModifierPoolInterface $pool)
    {
        $this->pool = $pool;
    }

    public function modify($value, array $options = array())
    {
        return $value;

        foreach ($options as $key => $value) {
            
        }

        if (empty($value)) {
            if (isset($options['string'])) {
                return $options['string'];
            }
            return null;
        }

        return $value;
    }

   

    public function testReturnDefault()
    {



        $this->assertNull(null);
    }
}
