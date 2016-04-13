<?php

namespace Nz\MigrationBundle\Controller\Crud;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Media\Media;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Nz\MigrationBundle\Admin\Traits\MigrationTrait;
use Nz\MigrationBundle\Migrator\MigratorHandler;
use Nz\MigrationBundle\Entity\Log as MigrationLog;

class MigrationAdminController extends Controller
{

    //use MigrationTrait;

    /**
     * Default action needed for sonata-admin menu builder to show in menu
     */
    public function listAction(Request $request = null)
    {
        return $this->render($this->admin->getTemplate('list'), array(
                'action' => 'list',
        ));
    }

    /**
     * Test action
     */
    public function migrateConfigAction(Request $request = null)
    {

        $persist = $request ? $request->get('persist', false) : false;
        /** @var \Nz\MigrationBundle\Migrator\MigratorHandler $migrator */
        $handler = $this->get('nz.migration.handler.default');
        $targets = $handler->migrateConfigObjects($persist);
        $errors = $handler->getErrors();
        $this->addFlash('sonata_flash_success', sprintf('<b>Success:</b> %d <br> %s', count($targets), implode('<br>', $targets)));
        $this->addFlash('sonata_flash_error', sprintf('<b>Errors:</b> %d <br>%s', count($errors), implode('<br>->', $errors)));

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
    /*
      WP
     */

    /**
     *  Show wp users info
     */
    public function usersInfo(Request $request = null)
    {
        /* $users = $this->getWpUsers(); */
        $not_like = [
            'closedpostboxes%', 'metaboxhidden%', '_wp%', 'wp_%', 'wpseo%', 'wppb%',
        ];

        $not_in = [
            'wpcf-group-form-toggle', 'users_per_page', 'use_ssl', 'upload_per_page', 'screen_layout_post', 'rich_editing', 'session_tokens',
            'nav_menu_recently_edited', 'show_admin_bar_front', 'show_welcome_panel', 'meta-box-order_post', 'managenav-menuscolumnshidden', 'last_login_time',
            'dismissed_wp_pointers', 'edit_agenda_per_page', 'comment_shortcuts', 'admin_color', '_yoast_wpseo_profile_updated', 'entry_id', 'googleplus'
        ];

        /* $em = $this->getWpEntityManager(); */
        /* $em = $this->get('doctrine')->getEntityManagerForClass(\Nz\WordpressBundle\Entity\UserMeta::class); */
        $em = $this->get('doctrine')->getEntityManagerForClass(\Nz\WordpressBundle\Entity\User::class);
        $qb = $em->createQueryBuilder();

        $qb
            ->select('m.key')
            ->from(\Nz\WordpressBundle\Entity\UserMeta::class, 'm')
            /* ->setMaxResults(10) */
            ->groupBy('m.key')
            ->orderBy('m.key', 'ASC')
            ->distinct()
        ;
        $this->buildQbWhere($qb, $not_like);
        $qb->andWhere($qb->expr()->notIn('m.key', $not_in));

        $metas = $qb->getQuery()->getResult();

        $rep = $this->get('doctrine')->getEntityManagerForClass(\Nz\WordpressBundle\Entity\User::class)->getRepository(\Nz\WordpressBundle\Entity\User::class);
        $users = $rep->findAll();

        return $this->render($this->admin->getTemplate('users-info'), array(
                'action' => 'users',
                'users' => $users,
                'metas' => $metas,
                'not_like' => $not_like,
                'not_in' => $not_in,
        ));
    }

    /**
     * Users info action
     */
    public function usersAction(Request $request = null)
    {
        return $this->usersInfo();
    }

    /**
     * migrate user(s)
     */
    public function migrateUsersAction(Request $request = null)
    {

        $persist = $request->get('persist', false);
        $id = $request->get($this->admin->getIdParameter());

        $rep = $this->get('doctrine')->getEntityManagerForClass(\Nz\WordpressBundle\Entity\User::class)->getRepository(\Nz\WordpressBundle\Entity\User::class);
        if ($id) {
            $users = $rep->findBy(array('id' => $id));
        } else {
            $users = $rep->findAll();
        }

        /** @var \Nz\MigrationBundle\Migrator\MigratorHandler $handler */
        $handler = $this->get('nz.migration.handler.default');
        $targets = $handler->migrate($users, $persist);
        $errors = $handler->getErrors();

        $this->addFlash('sonata_flash_success', sprintf('<b>Success:</b> %d <br> %s', count($targets), implode('<br>', $targets)));
        $this->addFlash('sonata_flash_error', sprintf('<b>Errors:</b> %d <br>%s', count($errors), implode('<br>->', $errors)));

        return new RedirectResponse($this->admin->generateUrl('users'));
    }

    public function postsInfo($handler = null)
    {
        $not_like = [
            '_wp%', '_edit%', '_menu%', '_yoast%', '_gform%', 'ml-slider%', '_oembed%', 'wpcf-event_flyer_bac%',
        ];

        $not_in = ['rule', 'spu_options', 'spu_rules', 'position',
            'enclosure', 'field_5332ac9bbfb6f', 'nzwpcm_ticketscript_event_id',
            //'_photo-gallery', 'photo-gallery'
        ];

        $excluded_types = $this->getParameter('nz.migration.wp.excluded_types');
        $types = $this->getPostsTypes($excluded_types);

        $metas = $this->getPostsMetasInfo($excluded_types, $not_in, $not_like);
        $posts = $this->get('doctrine')->getEntityManagerForClass(\Nz\WordpressBundle\Entity\Post::class)->getRepository(\Nz\WordpressBundle\Entity\Post::class)->findAll();

        return $this->render($this->admin->getTemplate('posts-info'), array(
                /* 'base_template' => 'NzMigrationBundle:CRUD:home.html.twig', */
                'action' => 'posts',
                'handler' => $handler,
                'posts' => $posts,
                'types' => $types,
                'excluded_types' => $excluded_types,
                'metas' => $metas,
                'not_like' => $not_like,
                'not_in' => $not_in,
        ));
    }

    /**
     * show list of post types
     * migrate selected post type
     */
    public function postsAction(Request $request)
    {

        return $this->postsInfo();
    }

    /**
     * migrate post(s)
     */
    public function migratePostsAction(Request $request = null)
    {

        $persist = $request->get('persist', false);
        $id = $request->get($this->admin->getIdParameter());

        $rep = $this->get('doctrine')->getEntityManagerForClass(\Nz\WordpressBundle\Entity\Post::class)->getRepository(\Nz\WordpressBundle\Entity\Post::class);
        if ($id) {
            $objects = $rep->findBy(array('id' => $id));
        } else {
            $objects = $rep->findBy(array('status' => 'publish'), null, 10);
        }

        $redirect = false;
        if (false === strpos($request->headers->get('referer'), '/admin/nz/migration')) {
            $redirect = true;
        }
        /* dd($objects); */
        ini_set('max_execution_time', 0);

        /** @var \Nz\MigrationBundle\Migrator\MigratorHandler $handler */
        $handler = $this->get('nz.migration.handler.default');
        $handler->migrate($objects, $persist);

        if (!$redirect) {
            return $this->postsInfo($handler);
        }
        $errors = $handler->getErrors();
        $targets = $handler->getTargets();
        /*
          d($objects);
          d($targets);
          dd($errors);
         */
        $this->addFlash('sonata_flash_success', sprintf('<b>Success:</b> %d <br> %s', count($targets), implode('<br>', $targets)));
        $this->addFlash('sonata_flash_error', sprintf('<b>Errors:</b> %d <br>%s', count($errors), implode('<br>->', $errors)));

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * Migrate post type
     */
    public function migrateTypeAction(Request $request)
    {
        $persist = $request->get('persist', false);
        $type = $request->get('type');

        /*$posts = $this->getNextMigrationPosts($type, 5);*/
        $posts = $this->getNextMigrationPosts($type, 30);
        ini_set('max_execution_time', 0);
        /* ini_set('memory_limit', '-1'); */

        /** @var \Nz\MigrationBundle\Migrator\MigratorHandler $handler */
        $handler = $this->get('nz.migration.handler.default');
        $handler->migrate($posts, $persist);
        return $this->postsInfo($handler);
    }

    private function getPostsMetasInfo($notIn, $notLike)
    {
        //get post types
        $em = $this->get('doctrine')//
            ->getEntityManagerForClass(\Nz\WordpressBundle\Entity\Post::class);

        /* $system_types = ['acf', 'nav_menu_item', 'revision', 'spucpt', 'wp-types-group', 'ml-slider']; */
        /* $excluded_types = $this->getParameter('nz.migration.wp.excluded_types'); */

        //get post metas
        $qb = $em->createQueryBuilder();
        $qb
            ->select('m.key')
            ->from(\Nz\WordpressBundle\Entity\PostMeta::class, 'm')
            ->groupBy('m.key')
            ->orderBy('m.key', 'ASC')
            /* ->setMaxResults(150) */
            /* ->groupBy('pm.key') */
            ->distinct();

        $this->buildQbWhere($qb, $notLike);
        $qb->andWhere($qb->expr()->notIn('m.key', $notIn));

        return $qb->getQuery()->getResult();
    }

    private function getPostsTypes($excludedTypes)
    {
        $em = $this->get('doctrine')//
            ->getEntityManagerForClass(\Nz\WordpressBundle\Entity\Post::class);

        $qb = $em->createQueryBuilder();

        return $qb
                ->select('p.type')
                ->from(\Nz\WordpressBundle\Entity\Post::class, 'p')
                ->where($qb->expr()->notIn('p.type', $excludedTypes))
                ->distinct()
                ->getQuery()
                ->getResult();
    }

    private function getNextMigrationPosts($type, $max = 10)
    {
        $migrated_ids = $this->get('doctrine')
            ->getEntityManagerForClass(MigrationLog::class)
            ->getRepository(MigrationLog::class)
            ->findMigratedSourceIdsBySource(\Nz\WordpressBundle\Entity\Post::class);

        $qb = $this->get('doctrine')
            ->getEntityManagerForClass(\Nz\WordpressBundle\Entity\Post::class)
            ->getRepository(\Nz\WordpressBundle\Entity\Post::class)
            ->getByTypeBuilder($type, 'publish');

        $qb->setMaxResults($max);

        if ($migrated_ids) {
            $qb->andWhere($qb->expr()->notIn('p.id', $migrated_ids));
        }

        return $qb->getQuery()->getResult();
    }

    private function buildQbWhere($qb, $conditions)
    {
        foreach ($conditions as $key => $v) {
            /* $qb->andWhere(sprintf('pm.key NOT LIKE ?%d', $key)); */
            $qb->andWhere($qb->expr()->notLike('m.key', sprintf('?%d', $key)));
        }
        $qb->setParameters($conditions);
    }
}
