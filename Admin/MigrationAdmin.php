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

    protected $baseRoutePattern = 'wp_migration'; //in url
    protected $baseRouteName = 'wp_migration_admin';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clear();
        $collection->add('list', 'list'); //home
        $collection->add('users', 'users');
        $collection->add('post-types', 'post-types');
        /* $collection->add('migrate-post-type', 'migrate-post-type'); */
        /* $collection->add('test', 'test'); */
        $collection->add('migrate-config', 'migrate-config');
        /* $collection->add('test-posts', 'test-posts'); */
        /* $collection->add('test-child', 'test-child'); */
    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        $home = $menu->addChild('Home', ['uri' => $this->generateUrl('list')]);

        if ($action == 'home') {
            $home->addChild('Migrate config', ['uri' => $this->generateUrl('migrate-config')]);
        }
        $users = $menu->addChild('Users', ['uri' => $this->generateUrl('users')]);
        
        if ($action == 'users') {
            $users->addChild('Metas', ['uri' => $this->generateUrl('users', ['sub' => 'metas'])]);
            $users->addChild('Migrate', ['uri' => $this->generateUrl('users', ['sub' => 'migrate'])]);
            
        }
        
        $poststypes = $menu->addChild('Post Types', ['uri' => $this->generateUrl('post-types')]);
        
        if ($action == 'post-types') {
            /*dd($this->getRouteGenerator()->generate('admin_nz_wordpress_post_list'));*/
            $poststypes->addChild('View Posts', ['uri' => $this->getRouteGenerator()->generate('admin_nz_wordpress_post_list')]);
        }
        /* $menu->addChild('Migrate config', ['uri' => $this->generateUrl('migrate-config')]); */

        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sidemenu.link_edit_post'), array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            $this->trans('sidemenu.post_metas', array(), 'NzShopBundle'), array('uri' => $admin->generateUrl('nz_wordpress.admin.post_meta.list', array('id' => $id)))
        );
    }

    public function getPersistentParameters()
    {
        if (!$this->getRequest()) {
            return array();
        }
        /* dd('ysfd'); */
        return array(
            'persist' => $this->getRequest()->get('persist', false)
            /* 'context'  => $this->getRequest()->get('context', 'default'), */
        );
    }
}
