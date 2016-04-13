<?php

namespace Nz\MigrationBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Nz\MigrationBundle\Entity\Log as MigrationLog;

class MigrationLogger
{

    /**
     * Avoid duplicate migration
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $target = $args->getEntity();

        // only act on some entity
        if (!$source = $this->getSource($target)) {
            return;
        }

        $rep = $args->getEntityManager()->getRepository(MigrationLog::class);
        $log = $rep->findOneBySource($source);
        if (!$log || $log->getError()) {
            return;
        }

        if (false !== strpos($log->getTarget(), get_class($target))) {
            throw new DuplicateMigrationException(sprintf('Duplicate target "%s:%d" for source "%s:%d"', $log->getTarget(), $log->getTargetId(), $log->getSource(), $log->getSourceId()));
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $target = $args->getEntity();

        // only act on some entity
        if (!$source = $this->getSource($target)) {
            return;
        }

        $log = new MigrationLog($source, $target);

        $entityManager = $args->getEntityManager();

        $entityManager->persist($log);
        $entityManager->flush();
    }

    private function getSource($target)
    {
        return isset($target->NzMigrationSrc) ? $target->NzMigrationSrc : false;
    }
}
