<?php

namespace Nz\MigrationBundle\Tests\Migrator;

use Nz\MigrationBundle\Tests\fixtures\TargetEntity;
/**
 *
 * @author tino
 */
class BaseMigratorTest extends \PHPUnit_Framework_TestCase
{

    public function testGeneralClass()
    {
        $migrator = $this->getMockBuilder('Nz\MigrationBundle\Migrator\BaseMigrator')
            /*->setConstructorArgs(array(TargetEntity::class))*/
            ->setMethods(array('isSrcMigrator', 'migrate'))
            ->getMock();
        
        /*$this->assertEquals(TargetEntity::class, $migrator->getClass());*/
        
        /*$migrator->setUpTarget();*/
        
        /*$this->assertEquals(new TargetEntity(), $migrator->getTarget());*/
    }
}
