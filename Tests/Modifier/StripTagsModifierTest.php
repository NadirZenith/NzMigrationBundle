<?php

namespace Nz\MigrationBundle\Tests\Modifier;

use Nz\MigrationBundle\Modifier\StripTagsModifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class StripTagsModifierTest extends \PHPUnit_Framework_TestCase
{

    protected function getModifier()
    {

        $modifier = $this->getMockBuilder(StripTagsModifier::class)
            ->setMethods(null)
            ->getMock();

        return $modifier;
    }

    public static function getTestData()
    {
        $options = array(
        );
        return array(
            array('<div>content</div>', 'content', $options),
        );
    }

    /**
     * @dataProvider getTestData
     */
    public function testStripTags($value, $result, $options = array())
    {
        $modifier = $this->getModifier();

        $this->assertEquals($result, $modifier->modify($value, $options));
    }
}
