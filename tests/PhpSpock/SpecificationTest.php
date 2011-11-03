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
                ->shouldReceive('run')->with($spec)->mock();

        $whenBlock = \Mockery::mock(SimpleBlock::clazz())
                ->shouldReceive('run')->with($spec)->mock();

        $thenBlock = \Mockery::mock(ThenBlock::clazz())
                ->shouldReceive('run')->with($spec)->mock();

        $whereBlock = \Mockery::mock(WhereBlock::clazz())
                ->shouldReceive('run')->with($spec)->mock();


        $spec->setSetupBlock($setupBlock);
        $spec->setWhenBlock($whenBlock);
        $spec->setThenBlock($thenBlock);

//        $spec->setWhereBlock($whereBlock);

        $spec->run();
    }
}
