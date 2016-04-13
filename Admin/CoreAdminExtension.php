<?php

namespace Nz\MigrationBundle\Admin;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\AdminInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;

class CoreAdminExtension extends AdminExtension implements ContainerAwareInterface
{

    protected $container;

    /**
     * {@inheritdoc}
     */
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection)
    {
        $collection->add('users', 'users');
        $collection->add('migrate-users', 'migrate-users');

        $collection->add('posts', 'posts');
        /*$collection->add('post-types', 'post-types');*/
        $collection->add('migrate-type', 'migrate-type');
        $collection->add('migrate-posts', 'migrate-posts');
    }

    public function configureSideMenu(AdminInterface $admin, MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        $users = $menu->addChild('WpUsers', ['uri' => $admin->generateUrl('users')]);

        if ($action == 'users') {
            $users->addChild('View Users', ['uri' => $admin->getRouteGenerator()->generate('admin_nz_wordpress_user_list')]);
            $users->addChild('Migrate', ['uri' => $admin->generateUrl('migrate-users')]);
        }

        $posts = $menu->addChild('WpPosts', ['uri' => $admin->generateUrl('posts')]);

        if ($action == 'posts') {
            /* dd($this->getRouteGenerator()->generate('admin_nz_wordpress_post_list')); */
            $posts->addChild('View Posts', ['uri' => $admin->getRouteGenerator()->generate('admin_nz_wordpress_post_list')]);
            $posts->addChild('Migrate', ['uri' => $admin->generateUrl('migrate-posts')]);
        }
        /* $menu->addChild('Migrate config', ['uri' => $this->generateUrl('migrate-config')]); */

        /*
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
         */
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
