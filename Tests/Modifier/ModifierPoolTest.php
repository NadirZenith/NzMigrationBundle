<?php

namespace Nz\MigrationBundle\Modifier;

/**
 * Description of MigratorPool
 *
 * @author tino
 */
class ModifierPoolTest extends \PHPUnit_Framework_TestCase
{

    protected function getPool()
    {
        $modifier1 = $this->getMockBuilder('Nz\MigrationBundle\Modifier\BooleanModifier')
            ->setMethods(null)
            ->getMock()
        ;
        $modifier2 = $this->getMockBuilder('Nz\MigrationBundle\Modifier\DatetimeModifier')
            ->setMethods(null)
            ->getMock()
        ;
        $modifier3 = $this->getMockBuilder('Nz\MigrationBundle\Modifier\StackModifier')
            ->setMethods(null)
            ->getMock()
        ;
        $modifier4 = $this->getMockBuilder('Nz\MigrationBundle\Modifier\RemoveTagModifier')
            ->setMethods(null)
            ->getMock()
        ;

        $pool = $this->getMockBuilder('Nz\MigrationBundle\Modifier\ModifierPool')
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
            ['stip_tags', ['allowable_tags' => '<a>']],
            /*['stip_tags'],*/
        ];

        $this->assertEquals('<a href="#">content</a>', $stack->modify('<div><a href="#">content</a></div>', $stacks));
        $this->assertEquals('<a href="#">content</a>', $stack->modify('<div id="ic"><a href="#">content</a>', $stacks));
        /*$this->assertEquals(true, $stack->modify(true, $stacks));*/



        $stacks = [
            ['stip_tags'],
        ];
        $this->assertEquals('content', $stack->modify('<div><a href="#">content</a></div>', $stacks));
    }
}
