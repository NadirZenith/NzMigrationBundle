<?php

namespace Nz\MigrationBundle\Modifier;

use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use AppBundle\Entity\Media\Media;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Description of StringModifier
 *
 * @author tino
 */
abstract class BaseCrawlerModifier implements ModifierInterface
{

    private $fixedTags = array();

    protected function fixMarkdownTags($value, $action)
    {
        if ('init' === $action) {
            $this->fixedTags = array();

            if (preg_match_all("/(<% .*? %>)/", $value, $matches)) {
                $this->fixedTags = array_pop($matches);
                foreach ($this->fixedTags as $k => $match) {
                    $value = str_replace($match, sprintf('<del>%d</del>', $k), $value);
                }
            }
        }
        if ('end' === $action) {
            if (preg_match_all("/<del>.*?<\/del>/", $value, $matches)) {
                foreach (array_pop($matches) as $k => $tag) {
                    $value = str_replace($tag, $this->fixedTags[$k], $value);
                }
            }
        }

        return $value;
    }

}
