<?php

namespace Nz\MigrationBundle\Tests\Migrator;

use Nz\MigrationBundle\Migrator\Wp\DefaultPostMigrator;
/* use Nz\WordpressBundle\Entity\User; */
use Nz\WordpressBundle\Entity\Post;
use Nz\WordpressBundle\Entity\PostMeta;
use Nz\MigrationBundle\Modifier\ModifierPool;
use Nz\MigrationBundle\Modifier\StringModifier;

/**
 *
 * @author tino
 */
class DefaultPostMigratorTest extends \PHPUnit_Framework_TestCase
{

    use TestTrait;

    private function getPostMigrator()
    {
        $modifierPool = new ModifierPool();
        $modifierPool->addModifier(new StringModifier(), 'string');

        $postMigrator = $this->getMockBuilder(DefaultPostMigrator::class)
            /* ->setConstructorArgs([TargetEntity::class]) */
            ->setMethods(null)
            ->getMock();

        $postMigrator->setModifierPool($modifierPool);

        $config = $this->getPostsConfig();
        $postMigrator->setConfig($config);
        return $postMigrator;
    }

    private function getTestPost()
    {

        $post = new Post();
        $post->setStatus('publish');
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
        return $this->getConfig()['wp']['posts'];
    }

    public function testPostMigratorException()
    {
        $this->setExpectedException(\Exception::class);

        $postMigrator = $this->getPostMigrator();
        $src = $this->getTestPost();
        $postMigrator->isSrcMigrator(new PostMeta());
    }

    public function testPostMigratorMigrate()
    {
        $postMigrator = $this->getPostMigrator();

        $src = $this->getTestPost();

        $this->assertEquals($postMigrator->isSrcMigrator($src), true);
        /*
          $falsey = clone $src;
          $falsey->setType('falsey');
          $this->assertEquals($postMigrator->isSrcMigrator($falsey), false);
          return;
         */

        $postMigrator->setUpTarget();
        $postMigrator->migrate($src);

        $target = $postMigrator->getTarget();
        $this->assertEquals($src->getTitle(), $target->getName());
        $this->assertEquals('Meta Value', $target->getTitle());
        $this->assertEquals('Meta Value 2', $target->getMeta('key'));
    }
}
