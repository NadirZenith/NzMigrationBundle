<?php

namespace Nz\MigrationBundle\Modifier;

use League\HTMLToMarkdown\HtmlConverter;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class HtmlToMarkdownModifier extends BaseCrawlerModifier
{

    public function modify($value, array $options = array())
    {

        $replace = [
            "", "\n"
        ];

        /* d($value); */
        //p open tag
        /* $newcontent = preg_replace("/<p[^>]*?>/", $replace[0], $value); */

        /* d($value); */
        /* str_replace('</p>', '<br />', $value); */
        //p close tag
        /* $value = str_replace("</p>", $replace[1], $newcontent); */

        $value = $this->fixMarkdownTags($value, 'init');

        $converter = new HtmlConverter();
        /* $html = "<h3>Quick, to the Batpoles!</h3>"; */
        $value = $converter->convert($value);

        $value = $this->fixMarkdownTags($value, 'end');
        /* dd($value); */
        return $value;
    }
}
