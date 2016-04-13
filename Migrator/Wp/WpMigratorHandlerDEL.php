<?php

namespace Nz\MigrationBundle\Migrator\Wp;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Types\Type;
use Nz\MigrationBundle\Migrator\MigratorHandlerInterface;
use Nz\MigrationBundle\Migrator\MigratorPoolInterface;

/**
 * Description of MigratorPool
 *
 * @author tino
 */
class WpMigratorHandler implements MigratorHandlerInterface
{

    protected $doctrine;
    protected $pool;
    protected $config = array();
    protected $errors = array();

    public function __construct(MigratorPoolInterface $pool, ManagerRegistry $doctrine)
    {
        $this->pool = $pool;
        $this->doctrine = $doctrine;
    }

    /**
     * Get src users not migrated
     */
    private function getUsers($maxResults = 10)
    {
        // Query already migrated users
        $class = $this->config['user']['target_entity'];
        $em2 = $this->getEntityManager($class);
        $qb2 = $em2->createQueryBuilder();
        $targets = $qb2
            ->select('t.wpId')
            ->from($class, 't')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
        ;

        // Query src users
        $class = $this->config['user']['src_entity'];
        $em = $this->getEntityManager($class);

        $qb = $em->createQueryBuilder();
        $qb
            ->select('o')
            ->from($class, 'o')
        ;

        // Not in migrated users
        if (!empty($targets)) {
            $qb
                ->where($qb->expr()->notIn('o.id', ':ids'))
                ->setParameter('ids', $targets, Type::SIMPLE_ARRAY)
            ;
        }
        $users = $qb
            ->setParameter('ids', $targets, Type::SIMPLE_ARRAY)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;

        return $users;
    }

    /**
     * Get wpId from already migrated posts
     */
    private function getPostsTargetsWpIds()
    {
        $targets = [];
        foreach ($this->config['posts'] as $type => $config) {
            $em2 = $this->getEntityManager($config['target_entity']);
            $qb2 = $em2->createQueryBuilder();
            $result = $qb2
                ->select('t.wpId')
                ->from($config['target_entity'], 't')
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
            ;
            $targets = array_merge($targets, $result);
        }
        return $targets;
    }

    /**
     * Get src objects (users & posts)
     */
    private function getSrcObjects($maxResults = 100)
    {

        $objects = $this->getUsers($maxResults);

        foreach ($this->config['posts'] as $type => $config) {
            $qb = $this->getEntityManager($config['src_entity'])->createQueryBuilder();
            $qb
                ->select('o')
                ->from($config['src_entity'], 'o')
                ->where($qb->expr()->like('o.type', ':type'))
                ->setParameter('type', $type)
                ->andWhere($qb->expr()->like('o.status', ':status'))
                ->setParameter('status', 'publish')
            ;
            $targets = $this->getPostsTargetsWpIds();
            if (!empty($targets)) {
                $qb
                    ->andWhere($qb->expr()->notIn('o.id', ':ids'))
                    ->setParameter('ids', $targets, Type::SIMPLE_ARRAY)
                ;
            }
            $posts = $qb
                ->setMaxResults($maxResults)
                ->getQuery()
                ->getResult()
            ;
            $objects = array_merge($objects, $posts);
        }

        return $objects;
    }

    public function migrateQueryBuilder($qb, $persist = false)
    {

        $wpids = $this->getPostsTargetsWpIds();
        if (!empty($wpids)) {
            $qb
                ->andWhere($qb->expr()->notIn('p.id', ':ids'))
                ->setParameter('ids', $wpids, Type::SIMPLE_ARRAY)
            ;
        }

        $srcs = $qb->getQuery()
            ->getResult()
        ;

        return $this->migrateObjects($srcs, $persist);
    }

    public function migrateConfigObjects($persist = false)
    {

        $srcs = $this->getSrcObjects();
        return $this->migrateObjects($srcs, $persist);
    }

    public function migrateObjects($srcs, $persist = false)
    {
        ini_set('max_execution_time', 0);

        $targets = [];
        foreach ($srcs as $src) {
            try {

                $target = $this->migrateSrc($src);
                if ($persist) {
                    $this->saveObject($target);
                }
                $targets[] = $target;
            } catch (\Doctrine\DBAL\DBALException $ex) {
                $this->addError($ex, $src, $target);
            }
        }

        return $targets;
    }

    public function migrateSrc($src)
    {

        $migrator = $this->pool->getMigratorForSrc($src);
        $migrator->setUpTarget();

        $migrator->migrateSrc($src);

        $filteredMetas = $this->filterMetas($src->getMetas()->toArray(), $migrator->getExcludedMetasKeysRegex());
        $migrator->migrateMetas($filteredMetas);

        return $migrator->getTarget();
    }

    /**
     * Filter wp metas and remove system only metas
     */
    protected function filterMetas(array $array_metas, array $filter_metas)
    {
        $metas = [];
        foreach ($array_metas as $meta) {

            if (false === $this->matchRegexArray($meta->getKey(), $filter_metas)) {
                $metas[] = $meta;
            }
        }

        return $metas;
    }

    protected function matchRegexArray($subject, array $regexes)
    {
        foreach ($regexes as $pattern) {
            if (preg_match($pattern, $subject)) {
                return true;
            }
        }
        return false;
    }

    public function setConfig($config)
    {
        $this->config = $config;
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

    public function saveObject($target)
    {
        $em = $this->getEntityManager(get_class($target));

        $em->persist($target);
        $em->flush();
    }

    private function addError($ex, $src, $target)
    {
        $this->errors[] = [
            'exception' => $ex,
            'name' => $src->__toString(),
            'src' => $src,
            'target' => $target,
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
