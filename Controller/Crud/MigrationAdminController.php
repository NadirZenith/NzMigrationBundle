<?php

namespace Nz\MigrationBundle\Controller\Crud;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Media\Media;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Nz\MigrationBundle\Admin\Traits\MigrationTrait;

class MigrationAdminController extends Controller
{

    use MigrationTrait;

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            $this->trans('sidemenu.link_edit_user'), array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );
        $menu->addChild(
            $this->trans('sidemenu.user_metas', array(), 'NzShopBundle'), array('uri' => $admin->generateUrl('nz_wordpress.admin.user_meta.list', array('id' => $id)))
        );
    }

    /**
     * Default action needed for sonata-admin menu builder to show in menu
     */
    public function listAction(Request $request = null)
    {
        return $this->render($this->admin->getTemplate('list'), array(
                'action' => 'home',
                'connection' => $this->getWpEntityManager()->getConnection(),
        ));
    }

    /**
     * get wp user(s)
     */
    public function getWpUsers($id = null)
    {
        $em = $this->getWpEntityManager();
        /* $users = $em->createQuery('SELECT u FROM Nz\WordpressBundle\Entity\User u')->getResult(); */

        $qb = $em->createQueryBuilder()
            ->select('u')
            ->from('Nz\WordpressBundle\Entity\User', 'u')
        //->setMaxResults(80) 
        /* ->getQuery() */
        ;

        if ($id) {
            $qb
                ->where('u.id LIKE :id')
                ->setParameter('id', $id)
            ;
        }

        $query = $qb->getQuery();
        $users = $query->getResult();

        return $users;
    }

    /**
     *  Show users
     */
    public function usersList(Request $request = null)
    {
        $users = $this->getWpUsers();

        return $this->render($this->admin->getTemplate('users'), array(
                'action' => 'users',
                'users' => $users,
        ));
    }

    /**
     * Show users metas
     */
    public function usersMetasList(Request $request = null)
    {
        $em = $this->getWpEntityManager();

        $not_like = [
            'closedpostboxes%', 'metaboxhidden%', '_wp%', 'wp_%', 'wpseo%', 'wppb%',
        ];

        $not_in = [
            'wpcf-group-form-toggle', 'users_per_page', 'use_ssl', 'upload_per_page', 'screen_layout_post', 'rich_editing', 'session_tokens',
            'nav_menu_recently_edited', 'show_admin_bar_front', 'show_welcome_panel', 'meta-box-order_post', 'managenav-menuscolumnshidden', 'last_login_time',
            'dismissed_wp_pointers', 'edit_agenda_per_page', 'comment_shortcuts', 'admin_color', '_yoast_wpseo_profile_updated', 'entry_id', 'googleplus'
        ];

        $qb = $em->createQueryBuilder();
        $qb
            ->select('m.key')
            ->from('Nz\WordpressBundle\Entity\UserMeta', 'm')
            /* ->setMaxResults(10) */
            ->groupBy('m.key')
            ->orderBy('m.key', 'ASC')
            ->distinct()
        ;
        $this->buildQbWhere($qb, $not_like);
        $qb->andWhere($qb->expr()->notIn('m.key', $not_in));

        $metas = $qb->getQuery()->getResult();

        return $this->render($this->admin->getTemplate('users_metas'), array(
                'action' => 'users-metas',
                'metas' => $metas,
                'not_like' => $not_like,
                'not_in' => $not_in,
        ));
    }

    /**
     * migrate user(s)
     */
    public function migrateUsersAction($id = null, $persist = false)
    {

        $users = $this->getWpUsers($id);

        $migrator = $this->get('wp.migrator');
        $migrated = $migrator->migrateObjects($users, $persist);
        $errors = $migrator->getErrors();

        $dump = [
            'migrated' => $migrated,
            'errors' => $errors,
        ];
        return $this->render($this->admin->getTemplate('home'), array(
                'action' => 'users',
                'dump' => $dump,
                'connection' => $this->getWpEntityManager()->getConnection(),
        ));
        /*
          $this->addFlash('sonata_flash_success', 'Cloned successfully');
          return new RedirectResponse($this->admin->generateUrl('list'));
         */
    }

    /**
     * Users action(list|migrate)
     */
    public function UsersAction(Request $request = null)
    {
        if ($request->get('sub') == 'metas') {

            return $this->usersMetasList();
        } else if ($request->get('sub') == 'migrate') {

            $id = $request->get('id', false);
            $persist = $request->get('persist', false);
            return $this->migrateUsersAction($id, $persist);
        } else {

            return $this->usersList();
        }
    }

    /**
     * Migrate post type
     */
    public function migratePostType($type, $persist)
    {

        $em = $this->getWpEntityManager();

        //get all published post types
        $qb = $em->createQueryBuilder();
        $objects = $qb
            ->select('p')
            ->from('Nz\WordpressBundle\Entity\Post', 'p')
            ->where($qb->expr()->like('p.type', ':type'))
            ->setParameter('type', $type)
            ->andWhere($qb->expr()->like('p.status', ':status'))
            ->setParameter('status', 'publish')
            /* ->setFirstResult(11) */
            ->setMaxResults(350)
        /* ->setMaxResults(10) */
        /* ->getQuery() */
        /* ->getResult() */
        ;
        $migrator = $this->get('wp.migrator');

        $migrated = $migrator->migrateQueryBuilder($qb, $persist);
        /* $migrated = $migrator->migrateObjects($objects, $persist); */
        $errors = $migrator->getErrors();

        $dump = [
            'migrated' => $migrated,
            'errors' => $errors,
        ];

        return $dump;
    }

    /**
     * show list of post types
     * migrate selected post type
     */
    public function postTypesAction(Request $request)
    {
        //migrate selected type if available
        $type = $request->get('type', false);
        $persist = $request->get('persist', false);
        $dump = [];
        if ($type) {
            $dump = $this->migratePostType($type, $persist);
        }


        //get post types
        $em = $this->getWpEntityManager();
        $system_types = ['acf', 'nav_menu_item', 'revision', 'spucpt', 'wp-types-group', 'ml-slider'];
        $qb = $em->createQueryBuilder();
        $types = $qb
            ->select('p.type')
            ->from('Nz\WordpressBundle\Entity\Post', 'p')
            ->where($qb->expr()->notIn('p.type', $system_types))
            ->distinct()
            ->getQuery()
            ->getResult();

        //get post metas
        //post metas
        $not_like = [
            '_wp%', '_edit%', '_menu%', '_yoast%', '_gform%', 'ml-slider%', '_oembed%', 'wpcf-event_flyer_bac%',
        ];

        $not_in = ['rule', 'spu_options', 'spu_rules', 'position',
            'enclosure', 'field_5332ac9bbfb6f', 'nzwpcm_ticketscript_event_id',
            /* '_photo-gallery', 'photo-gallery' */
        ];

        $qb = $em->createQueryBuilder();
        $qb
            ->select('m.key')
            ->from('Nz\WordpressBundle\Entity\PostMeta', 'm')
            ->groupBy('m.key')
            ->orderBy('m.key', 'ASC')
            /* ->setMaxResults(150) */
            /* ->groupBy('pm.key') */
            ->distinct();

        $this->buildQbWhere($qb, $not_like);
        $qb->andWhere($qb->expr()->notIn('m.key', $not_in));

        $metas = $qb->getQuery()->getResult();

                     return $this->render($this->admin->getTemplate('post_types'), array( 
                /* 'base_template' => 'NzMigrationBundle:CRUD:home.html.twig', */
                'action' => 'post-types',
                'types' => $types,
                'system_types' => $system_types,
                'metas' => $metas,
                'not_like' => $not_like,
                'not_in' => $not_in,
                'dump' => $dump,
        ));
    }

    /**
     * Test action
     */
    public function migrateConfigAction(Request $request = null)
    {

        $persist = $request ? $request->get('persist', false) : false;
        $migrator = $this->get('wp.migrator');

        $migrated = $migrator->migrateConfigObjects($persist);
        $errors = $migrator->getErrors();

        $dump = [
            'migrated' => $migrated,
            'errors' => $errors,
        ];
        return $this->render($this->admin->getTemplate('list'), array(
                'action' => 'home',
                'dump' => $dump,
                'connection' => $this->getWpEntityManager()->getConnection(),
        ));
    }

    private function buildQbWhere($qb, $conditions)
    {
        foreach ($conditions as $key => $v) {
            /* $qb->andWhere(sprintf('pm.key NOT LIKE ?%d', $key)); */
            $qb->andWhere($qb->expr()->notLike('m.key', sprintf('?%d', $key)));
        }
        $qb->setParameters($conditions);
    }

    /**
     *  Get entity manager
     * 
     *  @return \Doctrine\ORM\EntityManager Entity Manager
     */
    protected function getEntityManager()
    {
        if (!$this->get('doctrine')->getManager()->isOpen()) {
            $this->get('doctrine')->resetManager();
        }

        return $this->get('doctrine')->getManager();
    }

    /**
     *  Get entity manager
     * 
     *  @return \Doctrine\ORM\EntityManager Entity Manager
     */
    private function getWpPostRepository()
    {
        /* return $this->getWpEntityManager(); */
        $em = $this->getWpEntityManager();

        return $em->getRepository('NzWordpressBundle:Post');
    }

    /**
     *  Get entity manager
     * 
     *  @return \Doctrine\ORM\EntityManager Entity Manager
     */
    private function getWpEntityManager()
    {
        return $this->get(
                sprintf('doctrine.orm.%s_entity_manager', 'wp_cm')
        );
    }
}
