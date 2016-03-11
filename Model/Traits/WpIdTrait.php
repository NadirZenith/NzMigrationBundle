<?php

namespace Nz\MigrationBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of WpIdTrait
 *
 * @author tino
 */
trait WpIdTrait
{

    /**
     * @var integer $wpId
     *
     * @ORM\Column(name="wp_id", type="bigint")
     * <field name="wpId"   type="bigint"   column="wp_id"  unique="true"   nullable="true"    />
     */
    private $wpId;

    /**
     * Set wp id
     * 
     * @param integer $wpId
     */
    public function setWpId($wpId)
    {
        $this->wpId = $wpId;
        return $this;
    }

    /**
     * Get wp id
     * 
     * @return this Class
     */
    public function getWpId()
    {
        return $this->wpId;
    }
}
