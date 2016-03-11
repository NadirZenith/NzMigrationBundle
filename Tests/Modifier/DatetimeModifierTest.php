<?php

namespace Nz\MigrationBundle\Tests\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class DatetimeModifierTest extends \PHPUnit_Framework_TestCase
{
    
    

    protected function getModifier()
    {

        $modifier = $this->getMockBuilder('Nz\MigrationBundle\Modifier\DatetimeModifier')
            ->setMethods(null)
            ->getMock();

        return $modifier;
    }

    public static function getTestData()
    {
        return array(
            array(new \DateTime()),
            array('594518400'),
        );
    }

    /**
     * @dataProvider getTestData
     */
    public function testReturnDateTime($value, $options = array())
    {
        $modifier = $this->getModifier();

        $value = $modifier->modify($value, $options);
        $this->assertInstanceOf('DateTime', $value);
    }

    public function testReturnDefault()
    {
        $modifier = $this->getModifier();

        $value = $modifier->modify('nice', ['default' => 'default']);
        $this->assertEquals('default', $value);

        $this->assertNull($modifier->modify('nice'));
    }

    /**
     * @expectedException \Exception  
     */
    public function tesstReturnException()
    {
        $modifier = $this->getModifier();

        $modifier->modify('exception');
    }
}
