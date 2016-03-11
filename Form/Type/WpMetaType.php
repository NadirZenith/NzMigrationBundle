<?php

namespace Nz\MigrationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use AppBundle\Form\DataTransformer\DebugModelTransformer;
use AppBundle\Form\DataTransformer\DebugViewTransformer;
use AppBundle\Form\DataTransformer\WorkingModelTransformer;
use Nz\MigrationBundle\Form\DataTransformer\MetaSaveModelTransformer;

class WpMetaType extends AbstractType
{

    /*
     */

    public function __construct($kayue_wordpress)
    {
        $this->kayue_wordpress = $kayue_wordpress;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* dd(func_get_args()); */
        /*
          $debugModelTransformer = new \AppBundle\Form\DataTransformer\DebugModelTransformer();
          $workingModelTransformer = new \AppBundle\Form\DataTransformer\WorkingModelTransformer();
          $debugViewTransformer = new \AppBundle\Form\DataTransformer\DebugViewTransformer();
         */
        $builder
            ->add('key', 'text', array(
                'attr' => array(
                    'class' => 'meta_key')
                )
            )
            ->add('value', 'text', array(
                'attr' => array(
                    'class' => 'meta_value')
                )
            )
            //->addModelTransformer(new \Nz\ToolsBundle\Form\DataTransformer\DebugModelTransformer())
            //->addViewTransformer(new \Nz\ToolsBundle\Form\DataTransformer\DebugViewTransformer())
        /* ->addModelTransformer(new MetaSaveModelTransformer($this->kayue_wordpress)) */
        /* ->addViewTransformer($debugViewTransformer) */
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            /* 'data_class' => 'Nz\WordpressBundle\Entity\PostMeta' */
            'data_class' => null
        ));
    }

    public function getName()
    {
        return 'wp_metas';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent2()
    {
        /* new \Symfony\Component\Form\Extension\Core\Type\FormType(); */
        return 'Symfony\Component\Form\Extension\Core\Type\CollectionType';
    }
}
