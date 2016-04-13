<?php

namespace Nz\MigrationBundle\Twig;

/**
 * Description of MigrationExtension
 *
 * @author tino
 */
class MigrationExtension extends \Twig_Extension
{

    protected $logManager;

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('migration_status', //
                array($this, 'migrationStatus')
            ),
        );
    }

    public function migrationStatus($source)
    {
        return $this->logManager->findOneMigratedBySource($source);
    }

    public function setLogManager($logManager)
    {
        $this->logManager = $logManager;
    }

    public function getName()
    {
        return 'migration_extension';
    }
}
