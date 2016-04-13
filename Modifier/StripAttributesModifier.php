<?php

namespace Nz\MigrationBundle\Modifier;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class StripAttributesModifier extends BaseCrawlerModifier
{

    protected static $atts = ['style', 'class', 'title', 'id', 'src', 'href'];

    public function modify($value, array $options = array())
    {
        /*d($value);*/
        $options = $this->normalizeOptions($options);

        $removeAtts = array_diff(self::$atts, $options['allowable_attributes']);

        if (empty($removeAtts)) {
            return $value;
        }
        $value = $this->fixMarkdownTags($value, 'init');
        //fix charset = $doc->loadHTML('<?xml encoding="UTF-8">' . $value);
        $value = mb_convert_encoding($value, 'HTML-ENTITIES', $options['charset']);

        $doc = new \DOMDocument();
        $doc->loadHTML($value);
        $crawler = new Crawler($doc);

        //remove styles
        $p = $crawler->filter($options['filter']);
        foreach ($p as $x) {
            foreach ($removeAtts as $attr) {
                $x->removeAttribute($attr);
            }
        }

        $value = $this->fixMarkdownTags($crawler->children()->html(), 'end');

        /*dd($value);*/
        return $value;
    }

    public function normalizeOptions($options)
    {

        return $this->options = array_merge_recursive(array(
            'charset' => 'UTF-8',
            'filter' => '*', // 'p, a, span'
            'allowable_attributes' => [
                'title', 'src', 'href'
            ],
            ), $options);
    }
}
