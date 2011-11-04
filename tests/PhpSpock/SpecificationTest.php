<?php
/**
 * Date: 11/3/11
 * Time: 10:38 AM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock;

use \PhpSpock\Specification\SimpleBlock;
use \PhpSpock\Specification\ThenBlock;
use \PhpSpock\Specification\WhereBlock;

class SpecificationTest extends \PHPUnit_Framework_TestCase {

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }

    /**
     * @test
     */
    public function executeSpecification()
    {
        $spec = new Specification();
        
        $setupBlock = \Mockery::mock(SimpleBlock::clazz())
                ->shouldReceive('compileCode')->once()
                ->andReturn('$_ret = 1;')->mock();

        $whenBlock = \Mockery::mock(SimpleBlock::clazz())
                ->shouldReceive('compileCode')->once()
                ->andReturn('$_ret += 1;')->mock();

        $thenBlock = \Mockery::mock(ThenBlock::clazz())
                ->shouldReceive('compileCode')->once()
                ->andReturn('$_ret += 2;')->mock();


        $spec->setSetupBlock($setupBlock);
        $spec->setWhenBlock($whenBlock);
        $spec->setThenBlock($thenBlock);

//        $spec->setWhereBlock($whereBlock);


        
        $result = $spec->run();

        $this->assertEquals(4, $result);

    }



    /**
     * @test
     */
    public function executeSpecificationWithParametrization()
    {
        $spec = new Specification();

        $setupBlock = \Mockery::mock(SimpleBlock::clazz())
                ->shouldReceive('compileCode')->twice()
                ->andReturn('$_ret += 1;')->mock();

        $whenBlock = \Mockery::mock(SimpleBlock::clazz())
                ->shouldReceive('compileCode')->twice()
                ->andReturn('$_ret += 2;')->mock();

        $thenBlock = \Mockery::mock(ThenBlock::clazz())
                ->shouldReceive('compileCode')->twice()
                ->andReturn('$_ret += 3;')->mock();

        $whereBlock = \Mockery::mock(WhereBlock::clazz())
                ->shouldReceive('compileCode')->twice()
                // should receive 1, 2 in sequence 
                ->with(\Mockery::on(function($arg){
                    static $counter = 0;
                    $counter++;
                    return $arg == $counter;
                }))
                ->andReturn('$__parametrization__hasMoreVariants = true;', '$__parametrization__hasMoreVariants = false;')->mock();


        $spec->setSetupBlock($setupBlock);
        $spec->setWhenBlock($whenBlock);
        $spec->setThenBlock($thenBlock);
        $spec->setWhereBlock($whereBlock);

        $result = $spec->run();

        $this->assertEquals(12, $result);

    }
}
