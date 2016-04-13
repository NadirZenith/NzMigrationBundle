<?php

namespace Nz\MigrationBundle\Modifier;

use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class ImgTagModifier extends BaseCrawlerModifier
{

    /* const WP_UPLOADS_BASE_PATH = '/media/tino/data/sites/www/trendsmag/wp-content/uploads/'; */
    /* const WP_UPLOADS_URL_REPLACE = 'http://www.trendsmag.net/files/'; */
    /**
     * @var Pool
     */
    protected $mediaService;

    /**
     * @var ManagerInterface
     */
    protected $mediaManager;

    /**
     * @var CategoryManagerInterface
     */
    protected $categoryManager;

    /**
     * @var array
     */
    protected $options = array();

    public function __construct(ManagerInterface $mediaManager, CategoryManagerInterface $categoryManager)
    {
        $this->mediaManager = $mediaManager;
        $this->categoryManager = $categoryManager;
    }

    public function modify($value, array $options = array())
    {

        $value = $this->fixMarkdownTags($value, 'init');

        $this->normalizeOptions($options);
        //fix charset = $dom->loadHTML('<?xml encoding="UTF-8">' . $value);
        $value = mb_convert_encoding($value, 'HTML-ENTITIES', $this->options['charset']);
        $dom = new \DOMDocument();
        $dom->loadHTML($value); // @ to suppress a bajillion parse errors
        $dom->ecoding = $this->options['charset'];
        $crawler = new Crawler($dom);

        // will replace all images inside a nodes
        $crawler->filter('a')->each(function ($link) use ($dom) {
            //{% media media, 'small' %}
            //{% media media, 'small' with {'class': 'myclass'} %}
            $embed_format = '<%% media %d, "%s" %s%%>';
            /* $embed_format = '<%% media %d, "%s" %%>'; */

            foreach ($link as $node) {

                $linkimgs = $link->filter('img');
                if ($linkimgs->count() === 0) {
                    //no img inside link;
                    continue;
                }

                $ids = $this->processImgCrawler($linkimgs);
                if (empty($ids)) {
                    //no image replaced
                    continue;
                }

                //@todo process params
                $params = '';
                $content = '';
                foreach ($ids as $id) {
                    $content .= sprintf($embed_format, $id, $this->options['format'], $params);
                }

                $frag = $dom->createDocumentFragment();
                //allow unescaped chars
                $frag->appendXML(sprintf('<![CDATA[%s]]>', $content));
                $node->parentNode->replaceChild($frag, $node);
            }
        });

        $value = $this->fixMarkdownTags($crawler->children()->html(), 'end');

        return $value;
    }

    private function processImgCrawler($imgs)
    {

        $ids = [];
        foreach ($imgs as $img) {
            $imgurl = $img->getAttribute('src');
            $parseurl = parse_url($imgurl);
            $relativePath = str_replace($this->options['path_replace'][0], '', $parseurl['path']);

            $img_src = sprintf('%s%s', $this->options['path_replace'][1], $relativePath);
            if (!is_file($img_src)) {
                //tm fix
                $pathinfo = pathinfo($img_src);
                $img_src = sprintf('%s.%s', $pathinfo['dirname'], $pathinfo['extension']);
                if (!is_file($img_src)) {
                    throw new \Exception(sprintf('ImgTagModifier url: %s, src %s', $imgurl, $img_src));
                    /* continue; */
                }
            }

            $media = $this->createMedia($img_src, $img->getAttribute('alt'), $this->options['context']);

            $this->mediaManager->save($media, true);

            $ids[] = $media->getId();
        }

        return $ids;
    }

    private function createMedia($imgSrc, $title, $context, $createdAt = null)
    {
        
        if (!class_exists($this->options['media_class'])) {
            throw new \Exception(sprintf('ImgTagModifier media class does not exist (%s)',$this->options['media_class']));
        }
        $media = new $this->options['media_class']();

        $media->setName($title);
        $media->setCreatedAt(isset($createdAt) ? $createdAt : new \DateTime);
        $media->setBinaryContent($imgSrc);
        $media->setContext($context);
        $media->setProviderName('sonata.media.provider.image');
        $media->setCategory($this->getCategoryByContext($context));

        return $media;
    }

    protected function getCategoryByContext($context)
    {
        return $this->getCategoryManager()->findOneBy(['context' => $context]);
    }

    public function normalizeOptions($options)
    {

        return $this->options = array_merge(array(
            'media_class' => '\AppBundle\Entity\Media\Media',
            'format' => 'default',
            'context' => 'default',
            'charset' => 'UTF-8',
            'path_replace' => array('', ''),
            'checkWpId' => false,
            'required' => false,
            /*
              'base_path' => self::WP_UPLOADS_BASE_PATH,
              'url_replace' => self::WP_UPLOADS_URL_REPLACE,
             */
            ), $options);
    }

    public function getCategoryManager()
    {
        return $this->categoryManager;
    }
}
