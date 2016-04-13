<?php

namespace Nz\MigrationBundle\Tests\Migrator;

use Nz\MigrationBundle\Migrator\DefaultMigrator;
use Nz\MigrationBundle\Tests\fixtures\TargetEntity;
use Nz\MigrationBundle\Tests\fixtures\SourceEntity;
use Symfony\Component\Yaml\Parser;
use Nz\MigrationBundle\Modifier\ModifierPool;
use Nz\MigrationBundle\Modifier\StringModifier;

/**
 *
 * @author tino
 */
class DefaultMigratorTest extends \PHPUnit_Framework_TestCase
{

    use TestTrait;

    private function getMigrator()
    {
        $modifierPool = new ModifierPool();
        $modifierPool->addModifier(new StringModifier(), 'string');

        $migrator = $this->getMockBuilder(DefaultMigrator::class)
            /* ->setConstructorArgs([TargetEntity::class]) */
            ->setMethods(null)
            ->getMock();

        $migrator->setModifierPool($modifierPool);

        return $migrator;
    }

    private function getTestSrc()
    {

        $src = new SourceEntity();
        $src->setName('name');
        $src->setTitle('title');

        return $src;
    }

    public function testDefaultEntityMigration()
    {

        $postMigrator = $this->getMigrator();

        $config = $this->getConfig();
         /*dd($config); */
        $postMigrator->setConfig($config['default']['migrations']);
        $src = $this->getTestSrc();

        $this->assertEquals(false, $postMigrator->isSrcMigrator(new \stdClass()));
        $this->assertEquals(true, $postMigrator->isSrcMigrator($src));

        $postMigrator->migrate($src);

        $target = $postMigrator->getTarget();
        $this->assertEquals($src->getName(), $target->getName());
        $this->assertEquals($src->getTitle(), $target->getTitle());
    }
}
