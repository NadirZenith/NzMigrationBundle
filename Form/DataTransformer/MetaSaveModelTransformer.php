<?php

namespace Nz\MigrationBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Nz\WordpressBundle\Wordpress\ManagerRegistry;

class MetaSaveModelTransformer implements DataTransformerInterface
{
    private $em;
/*
    public function __construct(ManagerRegistry $kayue_wordpress)
 */
    public function __construct($kayue_wordpress)
    {
        $this->kayue_wordpress = $kayue_wordpress;
        /*dd($kayue_wordpress);*/
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($metas_collection)
    {
        /*dd($metas_collection);*/
        return $metas_collection;
    }
    
    public function setData($data){
        $this->data = $data;
        return $this;
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($metas_collection)
    {
        dd($metas_collection);
        /*$post = $metas_collection->first()->getPost();*/
        $post = $this->data;
        if ($post) {
            foreach ($post->getMetas() as $meta) {
                if (FALSE === $metas_collection->contains($meta)) {
                    $this->em->remove($meta);
                }
            }
        }

        return $metas_collection;
    }
}
