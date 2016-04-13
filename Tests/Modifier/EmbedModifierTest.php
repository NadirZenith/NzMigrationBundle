<?php

namespace Nz\MigrationBundle\Tests\Modifier;

use Nz\MigrationBundle\Modifier\EmbedModifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class EmbedModifierTest extends \PHPUnit_Framework_TestCase
{

    protected function getModifier()
    {

        $modifier = $this->getMockBuilder(EmbedModifier::class)
            ->setMethods(null)
            ->getMock();

        return $modifier;
    }

    public static function getTestData()
    {
        $youtube = "https://www.youtube.com/watch?v=VvORtNKouFM";
        $youtube2 = "https://www.youtube.com/watch?v=6A2V9Bu80J4";

        $soundcloud = "https://soundcloud.com/josepjosepjosep";
        $soundcloud2 = "https://soundcloud.com/duartdj/duart-frikiparty-2014";

        $mixcloud = "http://www.mixcloud.com/OddBcn/playlists/odd-is-house-a-cooltura-fm/";
        $mixcloud2 = "http://www.mixcloud.com/spartacus/party-time/";

        return array(
            #mixcloud
            array($mixcloud, "<% embed \"$mixcloud\" %>"),
            array("$mixcloud $mixcloud2", "<% embed \"$mixcloud\" %> <% embed \"$mixcloud2\" %>"),
            #youtube
            //simple url to embed
            array($youtube, "<% embed \"$youtube\" %>"),
            //link keep the same
            array("<a href=\"$youtube\">show</a>", "<a href=\"$youtube\">show</a>"),
            //multiple
            array("$youtube $youtube2",
                "<% embed \"$youtube\" %> <% embed \"$youtube2\" %>"
            ),
            //multiple repeated
            array("$youtube $youtube",
                "<% embed \"$youtube\" %> <% embed \"$youtube\" %>"
            ),
            #soundcloud
            //simple url to embed
            array($soundcloud, "<% embed \"$soundcloud\" %>"),
            //link keep the same
            array("<a href=\"$soundcloud\">show</a>", "<a href=\"$soundcloud\">show</a>"),
            //multiple
            array("$soundcloud $soundcloud2",
                "<% embed \"$soundcloud\" %> <% embed \"$soundcloud2\" %>"
            ),
            //multiple repeated
            array("$soundcloud $soundcloud",
                "<% embed \"$soundcloud\" %> <% embed \"$soundcloud\" %>"
            ),
            //url whith query arg
            array('https://soundcloud.com/raemorris/love-again?in=raemorris/sets/unguarded-the-debut-album', "<% embed \"https://soundcloud.com/raemorris/love-again?in=raemorris/sets/unguarded-the-debut-album\" %>"),
        );
    }

    /**
     * @dataProvider getTestData
     */
    public function testReturnEmbed($value, $result, $options = array())
    {
        $modifier = $this->getModifier();

        $this->assertEquals($result, $modifier->modify($value, $options));
    }
}
