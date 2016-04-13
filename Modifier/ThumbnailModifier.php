<?php

namespace Nz\MigrationBundle\Modifier;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use AppBundle\Entity\Media\Media;
use Nz\WordpressBundle\Entity\Post;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class ThumbnailModifier implements ModifierInterface
{

    const WP_UPLOADS_BASE_DIR = '/media/tino/data/sites/www/trendsmag/wp-content/uploads/';
    const WP_REMOTE_UPLOADS_BASE_DIR = '/home/clubber-mag/public_html/clubber-mag/wp-content/uploads/';
    const POST_CLASS = 'Nz\WordpressBundle\Entity\Post';
    private $managerRegistry;
    protected $categoryManager;
    protected $mediaManager;

    public function __construct(ManagerRegistry $managerRegistry, CategoryManagerInterface $categoryManager, $mediaManager)
    {
        $this->managerRegistry = $managerRegistry;
        $this->categoryManager = $categoryManager;
        $this->mediaManager = $mediaManager;
    }

    protected function getDoctrine()
    {
        return $this->managerRegistry;
    }

    /**
     *  Get entity manager
     * 
     *  @return \Doctrine\ORM\EntityManager Entity Manager
     */
    protected function getEntityManager($class)
    {

        if (!$this->getDoctrine()->getManagerForClass($class)->isOpen()) {
            $this->getDoctrine()->resetManager();
        }

        $em = $this->getDoctrine()->getManagerForClass($class);

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
        $options = $this->normalizeOptions($options);

        $post = $this->getEntityManager(Post::class)->find(Post::class, $value);
        if (!$post || 'attachment' !== $post->getType() || !in_array($post->getMimeType(), ["image/jpeg", "image/png"])) {
            if ($options['required']) {
                throw new \Exception(sprintf('Thumbnail Modifier media not found(mime-type/post-type)'));
            }
            return;
        }

        if ($options['checkWpId']) {
            /* @wpId must be implemented for this to work */
            if ($media = $this->findMedia($post->getId())) {
                return $media;
            }
        }


        $metas = $post->getMetas()->filter(function($meta) {
            if ('_wp_attached_file' == $meta->getKey()) {
                return $meta;
            }
        });

        if ($metas->isEmpty()) {
            if ($options['required']) {
                throw new \Exception(sprintf('Thumbnail Modifier media not found'));
            }
            return;
        }

        $img_src = self::WP_UPLOADS_BASE_DIR . $metas->first()->getValue();
        if (!is_file($img_src)) {
            if ($options['required']) {
                throw new \Exception(sprintf('Thumbnail Modifier file not found: %s', $img_src));
            }
            return;
        }


        $media = new Media();
        
        if ($options['checkWpId']) {
            $media->setWpId($post->getId());
        }
        
        $media->setName($post->getTitle());
        $media->setCreatedAt($post->getDate());
        $media->setBinaryContent($img_src);
        $media->setContext($options['context']);
        $media->setProviderName('sonata.media.provider.image');
        $media->setCategory($this->getCategoryByContext($options['context']));

        return $media;
    }

    public function normalizeOptions($options)
    {

        return array_merge(array(
            'context' => 'default',
            'checkWpId' => false,
            'required' => false,
            ), $options);
    }

    public function getCategoryManager()
    {
        return $this->categoryManager;
    }

    protected function getCategoryByContext($context)
    {
        return $this->getCategoryManager()->findOneBy(['context' => $context]);
    }
}
