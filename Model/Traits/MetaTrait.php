<?php

namespace Nz\MigrationBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of WpIdTrait
 *
 * @author tino
 */
trait MetaTrait
{

    /**
     * @var array $metas
     *
     * @ORM\Column(name="metas", type="json")
     * 
     * <field name="metas"         type="json"      column="metas"       />
     */
    private $metas = [];

    /**
     * Get all metas 
     */
    public function getMetas()
    {
        return $this->metas;
    }

    /**
     * Set all metas 
     */
    public function setMetas(array $metas = array())
    {
        $this->metas = $metas;

        return $this;
    }

    /**
     * Get meta by key
     */
    public function getMeta($meta)
    {
        if (!isset($this->metas[$meta])) {
            return;
        }

        return $this->metas[$meta];
    }

    /**
     * Set meta by key
     */
    public function setMeta($meta, $value)
    {
        $this->metas[$meta] = $value;

        return $this;
    }
}
