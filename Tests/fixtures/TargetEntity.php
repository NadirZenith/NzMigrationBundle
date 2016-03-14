<?php

namespace Nz\MigrationBundle\Tests\fixtures;

use Nz\MigrationBundle\Model\Traits\MetaTrait;

class TargetEntity
{
    use MetaTrait;

    protected $name;
    protected $metaKey;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getMetaKey()
    {
        return $this->metaKey;
    }

    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;
        return $this;
    }
}
