<?php

namespace Nz\MigrationBundle\Tests\Modifier;

use Nz\MigrationBundle\Tests\fixtures\EntityTest\EntityTest;
use Nz\MigrationBundle\Migrator\BaseMigrator;

/**
 *
 * @author tino
 */
class BaseMigratorTest extends \PHPUnit_Framework_TestCase
{

    public function testGeneralClass()
    {
        $baseMigrator = $this->getMockBuilder('Nz\MigrationBundle\Migrator\BaseMigrator')
            ->setConstructorArgs(array('Nz\MigrationBundle\Tests\fixtures\EntityTest\EntityTest'))
            ->getMock();

        $this->assertEquals($baseMigrator->getClass(), 'Nz\MigrationBundle\Tests\fixtures\EntityTest\EntityTest');
        /* $baseMigrator = new BaseMigrator(); */
    }
}
