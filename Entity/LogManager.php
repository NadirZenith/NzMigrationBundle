<?php

namespace Nz\MigrationBundle\Entity;

use Sonata\CoreBundle\Model\BaseEntityManager;
use Nz\MigrationBundle\Model\LogManagerInterface;

class LogManager extends BaseEntityManager implements LogManagerInterface
{

    /**
     * Find log for specific source
     * 
     * @param string        $source
     *
     * @return Object|null  Log
     */
    public function findOneBySource($source)
    {
        return $this->getRepository()->findOneBySource($source);
    }

    public function findOneMigratedBySource($source)
    {
        
        return $this->getRepository()->findOneMigratedBySource($source);
    }
}
