<?php

namespace Nz\MigrationBundle\Tests\Modifier;

use Nz\MigrationBundle\Modifier\StripAttributesModifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class StripAttributesModifierTest extends \PHPUnit_Framework_TestCase
{

    protected function getModifier()
    {

        $modifier = $this->getMockBuilder(StripAttributesModifier::class)
            ->setMethods(null)
            ->getMock();

        return $modifier;
    }

    public static function getTestData()
    {
        return array(
            array('<a href="#link" class="nice">content</a>', '<a href="#link">content</a>'),
        );
    }

    /**
     * @dataProvider getTestData
     */
    public function testStripAttributes($value, $result, $options = array())
    {
        $modifier = $this->getModifier();

        $this->assertEquals($result, $modifier->modify($value, $options));
    }
}
