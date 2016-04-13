<?php

namespace Nz\MigrationBundle\Modifier;


use Nz\MigrationBundle\Modifier;
/**
 * Description of MigratorPool
 *
 * @author tino
 */
class ModifierPoolTest extends \PHPUnit_Framework_TestCase
{

    protected function getPool()
    {
        $modifier1 = $this->getMockBuilder(Modifier\BooleanModifier::class)
            ->setMethods(null)
            ->getMock()
        ;
        $modifier2 = $this->getMockBuilder(Modifier\DatetimeModifier::class)
            ->setMethods(null)
            ->getMock()
        ;
        $modifier3 = $this->getMockBuilder(Modifier\StackModifier::class)
            ->setMethods(null)
            ->getMock()
        ;
        $modifier4 = $this->getMockBuilder(Modifier\StripTagsModifier::class)
            ->setMethods(null)
            ->getMock()
        ;

        $pool = $this->getMockBuilder(Modifier\ModifierPool::class)
            ->setMethods(null)
            ->getMock()
        ;

        $pool->addModifier($modifier1, 'boolean');
        $pool->addModifier($modifier2, 'datetime');
        $pool->addModifier($modifier3, 'stack');
        $pool->addModifier($modifier4, 'stip_tags');

        $modifier3->setPool($pool);

        return $pool;
    }

    public function testReturnDefault()
    {
        $pool = $this->getPool();
        
        $stack = $pool->getModifier('stack');

        $stacks = [
            /*['datetime', ['default' => 'nice']],*/
            /*['boolean', ['default' => 'trsdfue']],*/
            [null ,'stip_tags', ['allowable_tags' => '<a>']],
        ];

        $this->assertEquals('<a href="#">content</a>', $stack->modify('<div><a href="#">content</a></div>', $stacks));
        $this->assertEquals('<a href="#">content</a>', $stack->modify('<div id="ic"><a href="#">content</a>', $stacks));
        /*$this->assertEquals(true, $stack->modify(true, $stacks));*/



        $stacks = [
            ['stip_tags'],
        ];
        /*$this->assertEquals('content', $stack->modify('<div><a href="#">content</a></div>', $stacks));*/
    }
}
