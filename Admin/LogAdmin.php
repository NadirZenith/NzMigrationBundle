<?php

namespace Nz\MigrationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class LogAdmin extends Admin
{

    protected $datagridValues = array(
        '_page' => 1,
        '_per_page' => 320,
        '_sort_order' => 'DESC',
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        /*$collection->add('diff', 'diff');*/
        $collection->add('diff', $this->getRouterIdParameter().'/diff');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('source')
            ->add('sourceId')
            ->add('target')
            ->add('targetId')
            ->add('error')
            ->add('notes')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array(
                'class' => 'col-md-8',
            ))
            ->add('source')
            ->add('sourceId')
            ->add('target')
            ->add('targetId')
            ->add('error')
            ->add('notes')
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper
            ->addIdentifier('id')
            ->add('source', null)
            ->add('sourceId', null)
            ->add('target')
            ->add('targetId')
            ->add('error', null, array('editable' => true))
            ->add('_action', 'show', array(
                'actions' => array(
                    'show' => array(
                    /* 'template' => 'NzMigrationBundle:CRUD:list__wp_action.html.twig' */
                    ),
                    'diff' => array(
                        'template' => 'NzMigrationBundle:CRUD:list__log_action.html.twig'
                    )
                )
            ))
        ;
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {

        $datagridMapper
            ->add('source')
            ->add('target')
            ->add('error')
        ;
    }
}
