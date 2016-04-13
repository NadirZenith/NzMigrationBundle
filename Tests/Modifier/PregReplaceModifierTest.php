<?php

namespace Nz\MigrationBundle\Tests\Modifier;

use Nz\MigrationBundle\Modifier\PregReplaceModifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class PregReplaceModifierTest extends \PHPUnit_Framework_TestCase
{

    protected function getModifier()
    {

        $modifier = $this->getMockBuilder(PregReplaceModifier::class)
            ->setMethods(null)
            ->getMock();

        return $modifier;
    }

    public static function getTestData()
    {
        $options = array(
            /* 'patterns'  */
            'patterns' => array(
                '/(\[.*?\])(.+?)(\[\/.*?\])/',
            /* '/(\[.*?\])(?:.+?)(\[\/.*?\])/' */
            ),
            'replace' => ''
        );
        return array(
            array('this [shortcode]blablabla[/shortcode] continues', 'this  continues', $options),
            array('this [shortcode attr="value"]blablabla[/shortcode] continues', 'this  continues', $options),
            array('this [shortcode]one[/shortcode] continues [shortcode]two[/shortcode] after', 'this  continues  after', $options),
            array('[gallery ids="2,3,4"]', '[gallery ids="2,3,4"]', $options)
        );
    }

    /**
     * @dataProvider getTestData
     */
    public function testPregReplace($value, $result, $options = array())
    {
        $modifier = $this->getModifier();

        $this->assertEquals($modifier->modify($value, $options), $result);
    }
}
