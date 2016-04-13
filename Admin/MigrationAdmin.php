<?php

namespace Nz\MigrationBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;

class MigrationAdmin extends Admin
{

    protected $baseRoutePattern = 'nz/migration'; //in url
    protected $baseRouteName = 'admin_nz_migration';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clear();
        $collection->add('list', 'list');
        $collection->add('migrate-config', 'migrate-config');
        /* $collection->add('migrate-users', 'migrate-users'); */
    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {

        $request = $this->getRequest();
        $persist = $this->getRequest()->get('persist', false);

        $uri = $this->generateUrl($action, array_merge($request->attributes->get('_route_params'), array('persist' => !$persist)));
        $style = 'background-color:%s';
        $menu->addChild($persist ? $this->trans('sidemenu.link_persisting') : $this->trans('sidemenu.link_testing'), [
            'uri' => $uri,
            'attributes' => array(
                'style' => sprintf($style, $persist ? 'orangered' : 'greenyellow')
            )
        ]);
        $menu->addChild('Home', ['uri' => $this->generateUrl('list')]);
        $menu->addChild('Migrate config', ['uri' => $this->generateUrl('migrate-config')]);

        if ($action == 'home') {
            
        }
    }

    public function getPersistentParameters()
    {
        if (!$this->getRequest()) {
            return array();
        }
        return array(
            'persist' => $this->getRequest()->get('persist', false)
        );
    }
}
