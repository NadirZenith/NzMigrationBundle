<?php

namespace Nz\MigrationBundle\Migrator;

use Doctrine\Common\Persistence\ManagerRegistry;
use Nz\MigrationBundle\Entity\Log as MigrationLog;
use Nz\MigrationBundle\EventListener\DuplicateMigrationException;

/**
 * Description of MigratorPool
 *
 * @author tino
 */
class MigratorHandler implements MigratorHandlerInterface
{

    protected $doctrine;
    protected $pool;
    protected $config = array();
    protected $errors = array();
    protected $targets = array();
    protected $sources = array();

    public function __construct(MigratorPoolInterface $pool, ManagerRegistry $doctrine)
    {
        $this->pool = $pool;
        $this->doctrine = $doctrine;
    }

    public function migrateConfigObjects($persist = false)
    {
        $this->sources = array();
        $this->targets = array();
        foreach ($this->config['default']['migrations'] as $key => $conf) {

            if (!class_exists($conf['src_entity'])) {
                $this->errors[] = new \Exception(sprintf('Source class %s does not exist', $conf['src_entity']));
                continue;
            }

            //get srcs
            $em = $this->getEntityManager($conf['src_entity']);
            $rep = $em->getRepository($conf['src_entity']);
            $srcs = $rep->findAll();

            $this->sources = array_merge($this->sources, $srcs);
            $this->targets = array_merge($this->targets, $this->migrate($srcs, $persist));
        }

        return $this->targets;
    }

    public function migrate(array $sources, $persist = false)
    {

        $this->sources = array();
        $this->targets = array();
        foreach ($sources as $src) {
            $this->sources[] = $src;
            try {

                $target = $this->pool->getMigratorForSrc($src)->migrate($src);
                $this->targets[] = $target;

                $target->NzMigrationSrc = $src;

                if ($persist) {
                    $this->saveObject($target);
                }
            } catch (DuplicateMigrationException $ex) {
                $this->errors[] = $ex;
            } catch (\Exception $ex) {
                $log = new MigrationLog($src, isset($target) ? $target : null, $ex);
                $this->saveObject($log);
                $this->errors[] = $ex;
            }
        }
        /*dd($sources, $this->targets, $this->errors);*/
        return $this->targets;
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

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSources()
    {
        return $this->sources;
    }

    public function getTargets()
    {
        return $this->targets;
    }
}
