<?php

namespace Nz\MigrationBundle\Entity;

use Nz\MigrationBundle\Model\Log as LogModel;

/**
 * @author nz
 */
class Log extends LogModel
{

    /**
     * @var integer $id
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
