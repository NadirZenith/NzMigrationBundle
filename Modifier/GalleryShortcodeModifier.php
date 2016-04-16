<?php

namespace Nz\MigrationBundle\Modifier;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\ClassificationBundle\Entity\CategoryManager;
use Nz\WordpressBundle\Entity\Post;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class GalleryShortcodeModifier implements ModifierInterface
{

    /*
      const WP_UPLOADS_BASE_DIR = '/media/tino/data/sites/www/trendsmag/wp-content/uploads/';
      const WP_REMOTE_UPLOADS_BASE_DIR = '/home/clubber-mag/public_html/clubber-mag/wp-content/uploads/';
      const POST_CLASS = 'Nz\WordpressBundle\Entity\Post';
     */
    private $doctrine;
    protected $categoryManager;
    protected $mediaManager;
    protected $request;
    protected $options;

    public function __construct(ManagerRegistry $doctrine, CategoryManager $categoryManager, $mediaManager)
    {
        $this->doctrine = $doctrine;
        $this->categoryManager = $categoryManager;
        $this->mediaManager = $mediaManager;
    }

    /**
     *  Get entity manager
     * 
     *  @return \Doctrine\ORM\EntityManager Entity Manager
     */
    protected function getEntityManager($class)
    {

        if (!$this->doctrine->getManagerForClass($class)->isOpen()) {
            $this->doctrine->resetManager();
        }

        $em = $this->doctrine->getManagerForClass($class);

        if (!$em) {
            throw new Exception(sprintf('Can\'t find manager for class: %s', $class));
        }

        return $em;
    }

    public function findMedia($id)
    {
        $media = $this->mediaManager->findOneBy(['wpId' => $id]);
        return $media;
    }

    public function modify($value, array $options = array())
    {
        /* d($value); */
        $options = $this->normalizeOptions($options);
        list($groups, $matches) = $this->getGalleriesIds($value);
        if (empty($groups)) {
            if ($options['required']) {
                throw new \Exception(sprintf('Gallery Modifier no gallery shortcode find'));
            }

            return $value;
        }

        $new_gallery_tag = [];
        foreach ($groups as $group) {
            $medias = [];
            foreach ($group as $wpid) {

                $post = $this->getSourcePost($wpid);
                if (!$post) {
                    continue;
                }

                //check existent media
                if ($options['checkWpId'] && $media = $this->findMedia($post->getId())) {
                    $medias[] = $media;
                    continue;
                }

                if (!$img_rel = $this->getPostImage($post)) {
                    continue;
                }

                $img_src = rtrim($options['base_path'], '/') . '/' . trim($img_rel, '/');
                if (!is_file($img_src)) {
                    continue;
                }

                $media = $this->createMedia($img_src, $post->getTitle(), $options['context'], $post->getDate());
                if ($options['checkWpId']) {
                    $media->setWpId($post->getId());
                }

                $medias[] = $media;
            }
            if (empty($medias)) {
                continue;
            }

            $gallery = $this->createGallery($medias, $post->getTitle(), $options['context'], $options['default_format']);
            $new_gallery_tag[] = sprintf('<%% gallery %d, "%s" %%>', $gallery->getId(), $options['default_format']);
        }

        if (empty($new_gallery_tag)) {
            if ($options['required']) {
                throw new \Exception(sprintf('Gallery Modifier medias not found'));
            }
            return $value;
        }
        $value = str_replace($matches, $new_gallery_tag, $value);

        /* dd($value); */
        return $value;
    }

    /**
     * Finds a wordpress post with type attachment published 
     *  
     */
    private function getSourcePost($id)
    {
        $post = $this->getEntityManager(Post::class)->find(Post::class, $id);

        return !$post ||
            'attachment' !== $post->getType() ||
            !in_array($post->getMimeType(), ["image/jpeg", "image/png"]) ?
            null : $post;
    }

    private function createMedia($imgSrc, $title, $context, $createdAt = null)
    {

        if (!class_exists($this->options['media_class'])) {
            throw new \Exception(sprintf('GalleryShortcodeModifier media class does not exist (%s)', $this->options['media_class']));
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

    private function createGallery($medias, $title, $context, $default_format)
    {

        if (!class_exists($this->options['gallery_class'])) {
            throw new \Exception(sprintf('GalleryShortcodeModifier media class does not exist (%s)', $this->options['gallery_class']));
        }
        $gallery = new $this->options['gallery_class']();

        /* $gallery = new Gallery(); */
        $gallery->setName($title);
        $gallery->setContext($context);
        $gallery->setDefaultFormat($default_format);
        $gallery->setEnabled(true);
        foreach ($medias as $media) {
            $galleryMedia = new $this->options['gallery_has_media_class']();
            /* $galleryMedia = new GalleryHasMedia(); */
            $galleryMedia->setMedia($media);

            $gallery->addGalleryHasMedias($galleryMedia);
        }

        $this->mediaManager->save($gallery, true); //dynamic option from request some like @request->get(persist);

        return $gallery;
    }

    private function getPostImage($post)
    {
        $metas = $post->getMetas()->filter(function($meta) {
            if ('_wp_attached_file' == $meta->getKey()) {
                return $meta;
            }
        });

        if ($metas->isEmpty()) {
            return false;
        }

        return $metas->first()->getValue();
    }

    private function getGalleriesIds($value)
    {
        $shortcode = 'gallery';
        $gallery_pattern = "\[$shortcode(.*?)?\](?:(.+?)?\[\/$shortcode\])?";

        preg_match_all('/' . $gallery_pattern . '/s', $value, $matches);

        if (empty($matches)) {
            return [];
        }

        $ids = [];
        //" ids="240,122,119"
        foreach ($matches[1] as $gallery) {
            list($opt, $val) = explode("=", $gallery);
            $ids[] = explode(",", trim($val, '"'));
        }

        return [$ids, $matches[0]];
    }

    public function normalizeOptions($options)
    {

        return $this->options = array_merge(array(
            'media_class' => '\AppBundle\Entity\Media\Media',
            'gallery_class' => '\AppBundle\Entity\Media\Gallery',
            'gallery_has_media_class' => '\AppBundle\Entity\Media\GalleryHasMedia',
            'base_path' => '',
            'context' => 'default',
            'default_format' => 'abstract',
            'checkWpId' => false,
            'required' => false,
            ), $options);
    }

    protected function getCategoryByContext($context)
    {
        return $this->categoryManager->findOneBy(['context' => $context]);
    }

    public function setRequest(RequestStack $request)
    {
        $this->request = $request;
    }

    private function getRequest()
    {
        return $this->request->getCurrentRequest();
    }
}
