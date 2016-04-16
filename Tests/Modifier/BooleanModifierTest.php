<?php

namespace Nz\MigrationBundle\Tests\Modifier;

use Nz\MigrationBundle\Modifier\BooleanModifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class BooleanModifierTest extends \PHPUnit_Framework_TestCase
{

    protected function getModifier()
    {

        $modifier = $this->getMockBuilder(BooleanModifier::class)
            ->setMethods(null)
            ->getMock();

        return $modifier;
    }

    public static function getTestData()
    {
        return array(
            array(true, true),
            array(1, true),
            array('valid', true),
            array('on', true),
            array('ok', true),
            array('ko', false),
            array(2, true),
            array(0, false),
            array('off', false),
            array('nan', false, ['default' => false]),
            array('nan', true, ['default' => true]),
            array('true', true, ['default' => false]),
            array('false', false),
        );
    }

    /**
     * @dataProvider getTestData
     */
    public function testReturnBoolean($value, $result, $options = array())
    {
        $modifier = $this->getModifier();

        $this->assertEquals($modifier->modify($value, $options), $result);
    }
}
