<?php

namespace Nz\MigrationBundle\Admin;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;

class WpAdminExtension extends AdminExtension implements ContainerAwareInterface
{

    protected $container;

    /**
     * {@inheritdoc}
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {

        $collection->add('users', 'users');
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('_action', 'migrate', array(
                'actions' => array(
                    'Migrate' => array(
                        'template' => 'NzMigrationBundle:CRUD:list__wp_action.html.twig'
                    )
                )
            ))
        ;
        /* dd($listMapper); */
    }

    public function configureSideMenu(AdminInterface $admin, MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if ('edit' === $action && 'nz.wordpress.admin.post' === $admin->getCode()) {
            $menu->addChild('Migrate', ['uri' => $admin->getConfigurationPool()->getAdminByAdminCode('nz.migration.admin')->generateUrl('migrate-posts', array('id' => $admin->getSubject()->getId(), 'persist' => 1))]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPersistentParameters(AdminInterface $admin)
    {
        if (!$admin->getRequest()) {
            return array();
        }


        return array(
            'persist' => $admin->getRequest()->get('persist', false)
        );
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {

        $this->container = $container;
    }
}
