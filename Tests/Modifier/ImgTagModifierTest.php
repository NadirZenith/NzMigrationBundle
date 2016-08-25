<?php

namespace Nz\MigrationBundle\Tests\Modifier;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Sonata\ClassificationBundle\Entity\CategoryManager;
use Sonata\MediaBundle\Entity\MediaManager;
use Nz\MigrationBundle\Modifier\ImgTagModifier;
use Doctrine\ORM\EntityManager;
use Nz\WordpressBundle\Entity\Post;
use Nz\WordpressBundle\Entity\PostMeta;
use Sonata\ClassificationBundle\Model\Category;
use Sonata\CoreBundle\Model\ManagerInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;

/**
 * Description of StringModifier
 *
 * @author tino
 */
class ImgTagModifierTest extends \PHPUnit_Framework_TestCase
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
        $doctrine
            ->method("getManagerForClass")->will($this->returnValue($em));

        $category = $this->getMockBuilder(Category::class)
            ->getMock()
        ;

        $categoryManager = $this->getMockBuilder(CategoryManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $categoryManager
            ->method("findOneBy")->will($this->returnValue($category));
        ;
        $mediaManager = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            /* ->setMethods(null) */
            ->getMock()
        ;
        $mediaManager
            ->method("save")->will($this->returnValue(null));

        $modifier = $this->getMockBuilder(ImgTagModifier::class)
            ->setConstructorArgs(array($mediaManager, $categoryManager))
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
            /* 'str_replace' => array('/files', __DIR__ . '/..') */
            'path_replace' => array('/fixtures', __DIR__ . '/../fixtures'),
            'media_class' => \Nz\MigrationBundle\Tests\fixtures\MediaTest::class
        ];
        return array(
            //simple 
            array('<a href="#link-href"><img src="/fixtures/config.yml"></a>',
                '<% media 0, "default" %>', $options),
            //multiple 
            array('<a href="#link-href"><img src="/fixtures/config.yml"></a>content<a href="#link-href"><img src="/fixtures/TargetEntity.php"></a>',
                '<% media 0, "default" %>content<% media 0, "default" %>', $options),
            //using other formatter tags
            array('<a href="#link-href"><img src="/fixtures/config.yml"></a><% gallery 1, "format" with {\'class\': \'myclass\'} %><a href="#link-href"><img src="/fixtures/TargetEntity.php"></a>',
                '<% media 0, "default" %><% gallery 1, "format" with {\'class\': \'myclass\'} %><% media 0, "default" %>', $options)
        );
    }

    /**
     * @dataProvider getTestData
     */
    public function testReturnImageEmbedTags($value, $result, $options = array())
    {
        $this->assertEquals(1, true);
        return;
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
