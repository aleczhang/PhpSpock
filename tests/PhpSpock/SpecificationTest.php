<?php
/**
 * This file is part of PhpSpock.
 *
 * PhpSpock is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpSpock is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpSpock.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2011 Aleksandr Rudakov <ribozz@gmail.com>
 *
 **/
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
                ->andReturn('$__specification__assertCount = 1;')->mock();

        $whenBlock = \Mockery::mock(SimpleBlock::clazz())
                ->shouldReceive('compileCode')->twice()
                ->andReturn('$__specification__assertCount += 1;')->mock();

        $thenBlock = \Mockery::mock(ThenBlock::clazz())
                ->shouldReceive('compileCode')->twice()
                ->andReturn('$__specification__assertCount += 2;')->mock();


        $spec->setSetupBlock($setupBlock);

        $pair1 = new \PhpSpock\Specification\WhenThenPair();
        $pair1->setWhenBlock($whenBlock);
        $pair1->setThenBlock($thenBlock);

        $pair2 = new \PhpSpock\Specification\WhenThenPair();
        $pair2->setWhenBlock($whenBlock);
        $pair2->setThenBlock($thenBlock);

        $spec->setWhenThenPairs(array($pair1, $pair2));

        
        $result = $spec->run();

        $this->assertEquals(7, $result);

    }



    /**
     * @test
     */
    public function executeSpecificationWithParametrization()
    {
        $spec = new Specification();

        $setupBlock = \Mockery::mock(SimpleBlock::clazz())
                ->shouldReceive('compileCode')->twice()
                ->andReturn('$__specification__assertCount += 1;')->mock();

        $whenBlock = \Mockery::mock(SimpleBlock::clazz())
                ->shouldReceive('compileCode')->times(4)
                ->andReturn('$__specification__assertCount += 1;')->mock();

        $thenBlock = \Mockery::mock(ThenBlock::clazz())
                ->shouldReceive('compileCode')->times(4)
                ->andReturn('$__specification__assertCount += 2;')->mock();


        $spec->setSetupBlock($setupBlock);

        $pair1 = new \PhpSpock\Specification\WhenThenPair();
        $pair1->setWhenBlock($whenBlock);
        $pair1->setThenBlock($thenBlock);

        $pair2 = new \PhpSpock\Specification\WhenThenPair();
        $pair2->setWhenBlock($whenBlock);
        $pair2->setThenBlock($thenBlock);

        $spec->setWhenThenPairs(array($pair1, $pair2));

        
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
        $spec->setWhereBlock($whereBlock);

        $result = $spec->run();

        $this->assertEquals(14, $result);

    }
}
