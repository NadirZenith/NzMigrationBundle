<?php

namespace Nz\MigrationBundle\Modifier;

use Doctrine\Common\Persistence\ManagerRegistry;
use Nz\MigrationBundle\Entity\Log;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class LogTargetIdModifier implements ModifierInterface
{

    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function modify($value, array $options = array())
    {

        $log = $this->registry->getManagerForClass(Log::class)
            ->getRepository(Log::class)
            ->findOneMigratedBySource($value)
        ;

        if (!$log) {
            return null;
        }

        $value = $this->registry->getManagerForClass($log->getTarget())->find($log->getTarget(), $log->getTargetId());

        return $value;
    }
}
