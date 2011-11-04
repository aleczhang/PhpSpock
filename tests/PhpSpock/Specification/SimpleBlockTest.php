<?php
/**
 * Date: 11/3/11
 * Time: 10:38 AM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock\Specification;

class SimpleBlockTest extends \PHPUnit_Framework_TestCase {

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }

    /**
     * @test
     */
    public function compileCode()
    {
        $block = new SimpleBlock();
        $block->setCode('$foo = 1');

        $this->assertEquals('$foo = 1;', $block->compileCode());
    }
}
