<?php
/**
 * Date: 11/3/11
 * Time: 10:38 AM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock\Specification;

class ThenBlockTest extends \PHPUnit_Framework_TestCase {

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
        $foo = 0;
        
        $block = new ThenBlock();
        $block->setExpressions(
            array(
                '$foo = 1'
            )
        );

        $code = $block->compileCode();

        $this->assertNotNull($code);

        eval($code);
        $this->assertEquals(1, $foo);
    }

    /**
     * @test
     */
    public function testCodeWithBooleanExpressionThatIsTrue()
    {
        $foo = 0;

        $block = new ThenBlock();
        $block->setExpressions(
            array(
                '$foo = 1',
                '$foo == 1'
            )
        );

        $code = $block->compileCode();

        $this->assertNotNull($code);

        eval($code);
        $this->assertEquals(1, $foo);
    }

    /**
     * @test
     * @expectedException PhpSpock\Specification\AssertionException
     */
    public function testCodeWithBooleanExpressionThatIsFalse()
    {
        $foo = 0;

        $block = new ThenBlock();
        $block->setExpressions(
            array(
                '$foo = 1',
                '$foo == 2'
            )
        );

        $code = $block->compileCode();

        $this->assertNotNull($code);

        eval($code);
    }
}
