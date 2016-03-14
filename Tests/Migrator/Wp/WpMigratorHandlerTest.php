<?php

namespace Nz\MigrationBundle\Tests\Migrator;

use Nz\MigrationBundle\Modifier\ModifierPool;
use Nz\MigrationBundle\Modifier\StringModifier;
use Nz\MigrationBundle\Migrator\Wp\WpMigratorHandler;
use Nz\MigrationBundle\Migrator\Wp\DefaultPostMigrator;
use Symfony\Component\Yaml\Parser;
use Nz\MigrationBundle\Migrator\MigratorPool;
use Doctrine\Common\Persistence\ManagerRegistry;
use Nz\WordpressBundle\Entity\Post;
use Nz\WordpressBundle\Entity\PostMeta;

/**
 *
 * @author nz
 */
class WpMigratorHandlerTest extends \PHPUnit_Framework_TestCase
{

    private function getMigratorPool()
    {
        $modifierPool = new ModifierPool();
        $modifierPool->addModifier(new StringModifier(), 'string');

        $postMigrator = $this->getMockBuilder(DefaultPostMigrator::class)
            ->setConstructorArgs([TargetEntity::class])
            ->setMethods(null)
            ->getMock();

        $postMigrator->setModifierPool($modifierPool);
        $postMigrator->setConfig($this->getPostsConfig());

        $migratorPool = $this->getMockBuilder(MigratorPool::class)
            ->setMethods(null)
            ->getMock();

        $migratorPool->addMigrator($postMigrator);

        return $migratorPool;
    }

    private function getPostsConfig()
    {
        $yaml = new Parser();

        $config = $yaml->parse(file_get_contents(__DIR__ . '/../../fixtures/config.yml'));
        return $config['nz_migration']['posts'];
    }

    private function getTestPost()
    {

        $post = new Post();
        $post->setType('post');
        $post->setTitle('Post Title');

        $meta = new PostMeta();
        $meta->setKey('meta-key');
        $meta->setValue('Meta Value');
        $post->addMeta($meta);

        $meta = new PostMeta();
        $meta->setKey('meta-key-2');
        $meta->setValue('Meta Value 2');
        $post->addMeta($meta);

        return $post;
    }

    public function testGeneralClass()
    {

        $migratorPool = $this->getMigratorPool();

        $doctrine = $this->getMockBuilder(ManagerRegistry::class)
            ->getMock();
        $WpMigratorHandler = $this->getMockBuilder(WpMigratorHandler::class)
            ->setConstructorArgs([$migratorPool, $doctrine])
            ->setMethods(null)
            ->getMock();

        $src = $this->getTestPost();

        $target = $WpMigratorHandler->migrateObjects([$src])[0];

        $this->assertEquals($src->getTitle(), $target->getName());
        $this->assertEquals('Meta Value', $target->getMetaKey());
        $this->assertEquals('Meta Value 2', $target->getMeta('key'));

        $this->assertEquals(true, 1);
    }
}
