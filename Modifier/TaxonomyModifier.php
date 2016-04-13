<?php

namespace Nz\MigrationBundle\Modifier;

use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Nz\WordpressBundle\Entity\Post;
use AppBundle\Entity\Media\Gallery;
use AppBundle\Entity\Media\GalleryHasMedia;
use AppBundle\Entity\Media\Media;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class TaxonomyModifier implements ModifierInterface
{

    protected $managerRegistry;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->managerRegistry = $doctrine;
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

    public function modify($value, array $options = array())
    {
        $options = $this->normalizeOptions($options);

        $taxonomies = $value->filter(function($tax)use ($options) {
            if ($options['name'] === $tax->getName()) {
                return $tax;
            }
        });

        if (empty($taxonomies)) {
            return;
        }

        if ($options['multiple']) {
            $value = $this->buildMultiple($taxonomies, $options['target_class'], $options['context']);
        } else {
            $taxonomies = $taxonomies->toArray();
            $taxonomy = array_shift($taxonomies);
            $value = $this->buildSimple($taxonomy, $options['target_class'], $options['context']);
        }

        return $value;
    }

    public function normalizeOptions($options)
    {

        return $this->options = array_merge(array(
            'name' => "post_tag",
            'multiple' => TRUE,
            'target_class' => "AppBundle\Entity\Classification\Tag",
            'context_class' => "AppBundle\Entity\Classification\Context",
            'context' => "default"
            ), $options);
    }

    private function buildMultiple($taxonomies, $target_class, $context)
    {
        $collection = new ArrayCollection();
        foreach ($taxonomies as $tax) {
            $tag = $this->buildSimple($tax, $target_class, $context);

            $collection->add($tag);
        }

        return $collection;
    }

    private function buildSimple($taxonomy, $target_class, $context)
    {
        $rep = $this->managerRegistry->getManagerForClass($target_class)->getRepository($target_class);
        $tag = $rep->findOneBy(['name' => $taxonomy->getTerm()->getName()]);
        if (!$tag) {
            $tag = new $target_class();
            $tag->setEnabled(true);
            //fix for collection
            if (is_callable(array($tag, 'setDescription'))) {
                $tag->setDescription('n/a');
            }
            $tag->setContext($this->getContext($context));
            $tag->setName($taxonomy->getTerm()->getName());
        }

        return $tag;
    }

    private function getContext($name = 'default')
    {

        return $this->managerRegistry->getManagerForClass($this->options['context_class'])
                ->getRepository($this->options['context_class'])
                ->findOneBy(array('name' => $name));
    }
}
