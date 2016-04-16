<?php

namespace Nz\MigrationBundle\Tests\Modifier;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\MediaBundle\Entity\MediaManager;
use Nz\MigrationBundle\Modifier\GalleryShortcodeModifier;
use Nz\WordpressBundle\Entity\Post;
use Nz\WordpressBundle\Entity\PostMeta;
use Sonata\ClassificationBundle\Model\Category;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class GalleryShortcodeModifierTest extends \PHPUnit_Framework_TestCase
{

    protected function getRandWpPostAttachment()
    {

        $meta = new PostMeta();
        $meta->setKey('_wp_attached_file');
        $meta->setValue('/../fixtures/config.yml');

        $post = new Post();
        $post->setTitle('jaaj');
        $post->setType('attachment');
        $post->setMimeType("image/jpeg");
        $post->addMeta($meta);

        return $post;
    }

    protected function getModifier()
    {
        //EntityRepository
        $er = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $er
            ->method("isOpen")->will($this->returnValue(true));

        $post = $this->getRandWpPostAttachment();
        //EntityManager
        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $em->method("isOpen")->will($this->returnValue(true));
        $em->method("find")->will($this->returnValue($post));


        $doctrine = $this->getMockBuilder(ManagerRegistry::class)
            ->getMock()
        ;
        $doctrine->method("getManagerForClass")->will($this->returnValue($em));

        $categoryManager = $this->getMockBuilder(CategoryManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $category = $this->getMockBuilder(Category::class)
            ->getMock()
        ;
        $categoryManager
            ->method("findOneBy")->will($this->returnValue($category));
        ;
        $mediaManager = $this->getMockBuilder(MediaManager::class)
            ->disableOriginalConstructor()
            /* ->setMethods(null) */
            ->getMock()
        ;
        $mediaManager
            ->method("save")->will($this->returnValue(null));
        $modifier = $this->getMockBuilder(GalleryShortcodeModifier::class)
            ->setConstructorArgs(array($doctrine, $categoryManager, $mediaManager))
            ->setMethods(null)
            ->getMock()
        ;
        $modifier
            ->method('getSourcePost')->will($this->returnValue('hahahah'))
        ;

        return $modifier;
    }

    public static function getTestData()
    {
        $options = [
            'base_path' => __DIR__,
            'media_class' => \Nz\MigrationBundle\Tests\fixtures\MediaTest::class,
            'gallery_class' => \Nz\MigrationBundle\Tests\fixtures\GalleryTest::class,
            'gallery_has_media_class' => \Nz\MigrationBundle\Tests\fixtures\GalleryHasMediaTest::class
        ];
        return array(
            array('[gallery ids="240,122,119"]', '<% gallery 0, "abstract" %>', $options),
            array('<p>[gallery ids="246,247,249,250"]</p>', '<p><% gallery 0, "abstract" %></p>', $options),
            array('content[gallery ids="240,122,119"]content[gallery ids="34,32,43"]', 'content<% gallery 0, "abstract" %>content<% gallery 0, "abstract" %>', $options)
        );
    }

    /**
     * @dataProvider getTestData
     */
    public function testReturnGalleryEmbedTags($value, $result, $options = array())
    {
        $modifier = $this->getModifier();

        $this->assertEquals($modifier->modify($value, $options), $result);
    }

    /**
     * expectedException \RuntimeException
     */
    public function tes2tReturnException()
    {
        $modifier = $this->getModifier();

        $modifier->modify('exception');
    }
}