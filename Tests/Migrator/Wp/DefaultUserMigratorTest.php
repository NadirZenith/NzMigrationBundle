<?php

namespace Nz\MigrationBundle\Tests\Migrator;

use Nz\MigrationBundle\Migrator\Wp\DefaultUserMigrator;
use Nz\MigrationBundle\Tests\fixtures\TargetEntity;
/* use Nz\WordpressBundle\Entity\User; */
use Nz\WordpressBundle\Entity\User;
use Nz\WordpressBundle\Entity\UserMeta;
use Symfony\Component\Yaml\Parser;
use Nz\MigrationBundle\Modifier\ModifierPool;
use Nz\MigrationBundle\Modifier\StringModifier;

/**
 *
 * @author tino
 */
class DefaultUserMigratorTest extends \PHPUnit_Framework_TestCase
{

    use TestTrait;

    private function getTestUser()
    {

        $user = new User();
        $user->setUsername('Nadir');

        $meta = new UserMeta();
        $meta->setKey('meta-key');
        $meta->setValue('Meta Value');
        $user->addMeta($meta);

        $meta = new UserMeta();
        $meta->setKey('meta-key-2');
        $meta->setValue('Meta Value 2');
        $user->addMeta($meta);
        return $user;
    }

    public function testGeneralClass()
    {
        $modifierPool = new ModifierPool();
        $modifierPool->addModifier(new StringModifier(), 'string');

        /* $postMigrator = $this->get */
        // Create a stub for the SomeClass class.
        $migrator = $this->getMockBuilder(DefaultUserMigrator::class)
            /* ->setConstructorArgs([TargetEntity::class]) */
            ->setMethods(null)
            ->getMock();

        $migrator->setModifierPool($modifierPool);


        $config = $this->getUserConfig();
        $migrator->setConfig($config);

        $src = $this->getTestUser();

        $this->assertEquals($migrator->isSrcMigrator($src), true);

        $migrator->setUpTarget();
        $migrator->migrate($src);

        $target = $migrator->getTarget();

        $this->assertEquals($src->getUsername(), $target->getName());
        $this->assertEquals('Meta Value', $target->getTitle());
        $this->assertEquals('Meta Value 2', $target->getMeta('key'));
    }

    private function getUserConfig()
    {
        return $this->getConfig()['wp']['user'];
    }
}
