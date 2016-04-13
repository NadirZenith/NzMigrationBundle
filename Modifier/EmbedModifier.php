<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class EmbedModifier implements ModifierInterface
{

    private function getRegexes()
    {
        return array(
            "/(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\#\?&\"'\[\s\r\n><]+)/", //youtube
            "/https?:\/\/(?:soundcloud.com|snd.sc)\/([^\s\#&\"'><]+)/", //soundcloud
            "/http:\/\/(?:www\.mixcloud\.com)\/([^\s\#&\"'><]+)/", //mixcloud
        );
    }

    public function modify($value, array $options = array())
    {

        $embed_format = '<%% embed "%s" %%>';
        $data = [];
        foreach ($this->getRegexes() as $regex) {
            if (preg_match_all($regex, $value, $matches)) {
                $urls = array_map('trim', $matches[0]);

                foreach ($urls as $url) {
                    //is link
                    if (false !== strpos($value, sprintf('href="%s"', $url))) {
                        continue;
                    }

                    $data[$url] = sprintf($embed_format, $url);
                }
            }
        }

        /* dd($data); */
        if (!empty($data)) {

            $value = str_replace(array_keys($data), array_values($data), $value);
        }

        return $value;
    }
}
