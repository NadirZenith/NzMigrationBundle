<?php

namespace Nz\MigrationBundle\Tests\Migrator;

use Nz\MigrationBundle\Migrator\Wp\DefaultPostMigrator;
use Nz\MigrationBundle\Tests\fixtures\TargetEntity;
/* use Nz\WordpressBundle\Entity\User; */
use Nz\WordpressBundle\Entity\Post;
use Nz\WordpressBundle\Entity\PostMeta;
use Symfony\Component\Yaml\Parser;
use Nz\MigrationBundle\Modifier\ModifierPool;
use Nz\MigrationBundle\Modifier\StringModifier;

/**
 *
 * @author tino
 */
class DefaultPostMigratorTest extends \PHPUnit_Framework_TestCase
{

    private function getPostMigrator()
    {
        $modifierPool = new ModifierPool();
        $modifierPool->addModifier(new StringModifier(), 'string');

        $postMigrator = $this->getMockBuilder(DefaultPostMigrator::class)
            ->setConstructorArgs([TargetEntity::class])
            ->setMethods(null)
            ->getMock();

        $postMigrator->setModifierPool($modifierPool);


        $postMigrator->setConfig($this->getPostsConfig());
        return $postMigrator;
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

    private function getPostsConfig()
    {
        $yaml = new Parser();

        $config = $yaml->parse(file_get_contents(__DIR__ . '/../../fixtures/config.yml'));
        return $config['nz_migration']['posts'];
    }

    public function testGeneralClass()
    {
        $postMigrator = $this->getPostMigrator();

        $src = $this->getTestPost();

        $this->assertEquals($postMigrator->isSrcMigrator($src), true);
        $this->assertEquals($postMigrator->isSrcMigrator(new PostMeta()), false);

        $falsey = clone $src;
        $falsey->setType('falsey');
        $this->assertEquals($postMigrator->isSrcMigrator($falsey), false);

        $postMigrator->setUpTarget();
        $postMigrator->migrateSrc($src);

        $postMigrator->migrateMetas($src->getMetas()->toArray());

        $target = $postMigrator->getTarget();


        $this->assertEquals($src->getTitle(), $target->getName());
        $this->assertEquals('Meta Value', $target->getMetaKey());
        $this->assertEquals('Meta Value 2', $target->getMeta('key'));
    }
}
