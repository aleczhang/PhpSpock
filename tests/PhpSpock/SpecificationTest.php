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
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with PhpSpock.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2011 Aleksandr Rudakov <ribozz@gmail.com>
 *
 **/
/**
 * Date: 11/3/11
 * Time: 10:38 AM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
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
    public function eventBeforeGeneration()
    {
        $ed = \Mockery::mock('Symfony\Component\EventDispatcher\EventDispatcher')
                ->shouldReceive('dispatch')->withAnyArgs()->andReturnUsing(
                    function($eventName, Event $event) {

                        if ($eventName == Event::EVENT_BEFORE_CODE_GENERATION) {
                            $event->setAttribute('code', '$__specification__assertCount = 123;');
                        }
                    }
                )->mock();

        $spec = new Specification();
        $spec->setEventDispatcher($ed);

        $this->assertEquals(123, $spec->run());
    }

    /**
     * @test
     */
    public function eventModifyAssertCountGeneration()
    {
        $ed = \Mockery::mock('Symfony\Component\EventDispatcher\EventDispatcher')
                ->shouldReceive('dispatch')->withAnyArgs()->andReturnUsing(
                    function($eventName, Event $event) {

                        if ($eventName == Event::EVENT_BEFORE_CODE_GENERATION) {
                            $event->setAttribute('code', '$__specification__assertCount = 0;');
                        }

                        if ($eventName == Event::EVENT_MODIFY_ASSERTION_COUNT) {
                            $event->setAttribute('count', $event->getAttribute('count') + 2);
                        }
                    }
                )->mock();

        $spec = new Specification();
        $spec->setEventDispatcher($ed);

        $this->assertEquals(2, $spec->run());
    }

    /**
     * @test
     */
    public function eventCollectVariables()
    {
        $ed = \Mockery::mock('Symfony\Component\EventDispatcher\EventDispatcher')
                ->shouldReceive('dispatch')->withAnyArgs()->andReturnUsing(
                    function($eventName, Event $event) {

                        if ($eventName == Event::EVENT_COLLECT_EXTRA_VARIABLES) {
                            $event->setAttribute('baz', function($arg){return $arg * 5;}); // injecting own closure
                        }
                    }
                )->mock();

        $setupBlock = \Mockery::mock(ThenBlock::clazz())
                ->shouldReceive('compileCode')->once()
                ->andReturn('$__specification__assertCount = $baz(4);')->mock();

        $spec = new Specification();
        $spec->setEventDispatcher($ed);

        $spec->setSetupBlock($setupBlock);

        $this->assertEquals(20, $spec->run());
    }



    /**
     * @test
     */
    public function eventConvertExceptions()
    {
        $ed = \Mockery::mock('Symfony\Component\EventDispatcher\EventDispatcher')
                ->shouldReceive('dispatch')->withAnyArgs()->andReturnUsing(
                    function($eventName, Event $event) {

                        if ($eventName == Event::EVENT_TRANSFORM_TEST_EXCEPTION) {

                            $e = $event->getAttribute('exception');
                            $e = new \BadMethodCallException($e->getMessage() . 'barbaz');

                            $event->setAttribute('exception', $e);
                        }
                    }
                )->mock();

        $setupBlock = \Mockery::mock(ThenBlock::clazz())
                ->shouldReceive('compileCode')->once()
                ->andReturn('throw new \Exception("foo");')->mock();

        $spec = new Specification();
        $spec->setEventDispatcher($ed);

        $spec->setSetupBlock($setupBlock);

        try {
            $spec->run();
            $this->fail('Exception expected!');
            
        } catch(\BadMethodCallException $exc) {
            $this->assertEquals('foobarbaz', $exc->getMessage());
        }

    }

    /**
     * @test
     */
    public function eventConvertExceptionsWithExceptionRemoved()
    {
        $ed = \Mockery::mock('Symfony\Component\EventDispatcher\EventDispatcher')
                ->shouldReceive('dispatch')->withAnyArgs()->andReturnUsing(
                    function($eventName, Event $event) {

                        if ($eventName == Event::EVENT_TRANSFORM_TEST_EXCEPTION) {
                            $event->setAttribute('exception', null);
                        }
                    }
                )->mock();

        $setupBlock = \Mockery::mock(ThenBlock::clazz())
                ->shouldReceive('compileCode')->once()
                ->andReturn('$__specification__assertCount = 1; throw new \Exception("foo");')->mock();

        $spec = new Specification();
        $spec->setEventDispatcher($ed);

        $spec->setSetupBlock($setupBlock);

        $spec->run();
        // no exception should be here
    }



    /**
     * @test
     */
    public function eventDebug()
    {
        $ed = \Mockery::mock('Symfony\Component\EventDispatcher\EventDispatcher')
                ->shouldReceive('dispatch')->withAnyArgs()->andReturnUsing(
                    function($eventName, Event $event) {

                        if ($eventName == Event::EVENT_DEBUG) {
                            $event->setAttribute('result', "foo");
                        }
                    }
                )->mock();

        $spec = new Specification();
        $spec->setEventDispatcher($ed);

        $result = $spec->run();
        $this->assertEquals('foo', $result);
    }

    /**
     * @test
     */
    public function mockObjectGeneration()
    {
        $spec = new Specification();

        $setupBlock = \Mockery::mock(SimpleBlock::clazz())
                ->shouldReceive('compileCode')->once()
                ->andReturn('$__specification__assertCount = 1;')->mock();

        $whenBlock = \Mockery::mock(SimpleBlock::clazz())
                ->shouldReceive('compileCode')->once()
                ->andReturn('if($foo instanceof \Mockery\MockInterface) $__specification__assertCount += 1;')->mock();

        $thenBlock = \Mockery::mock(ThenBlock::clazz())
                ->shouldReceive('compileCode')->once()
                    ->andReturn('if($baz instanceof \Mockery\MockInterface) $__specification__assertCount += 2;')
                ->shouldReceive('getPreConditions')->once()
                    ->andReturn(array('$__specification__assertCount += 3'))->mock();  // and +1 for preconditions


        $spec->setSetupBlock($setupBlock);
        $pair1 = new \PhpSpock\Specification\WhenThenPair();
        $pair1->setWhenBlock($whenBlock);
        $pair1->setThenBlock($thenBlock);
        $spec->setWhenThenPairs(array($pair1));

        $spec->setVarDeclarations(array(
              'foo' => '',
              'baz' => '',
          ));

        $result = $spec->run();

        $this->assertEquals(8, $result);

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
                ->andReturn('$__specification__assertCount += 2;')
                ->shouldReceive('getPreConditions')->twice()
                    ->andReturn(array('$__specification__assertCount += 3'))->mock(); // and +2 for preconditions

        $cleanupBlock = \Mockery::mock(SimpleBlock::clazz())
                ->shouldReceive('compileCode')->once()
                ->andReturn('$__specification__assertCount += 3;')->mock();


        $spec->setSetupBlock($setupBlock);

        $pair1 = new \PhpSpock\Specification\WhenThenPair();
        $pair1->setWhenBlock($whenBlock);
        $pair1->setThenBlock($thenBlock);

        $pair2 = new \PhpSpock\Specification\WhenThenPair();
        $pair2->setWhenBlock($whenBlock);
        $pair2->setThenBlock($thenBlock);

        $spec->setWhenThenPairs(array($pair1, $pair2));

        $spec->setCleanupBlock($cleanupBlock);

        
        $result = $spec->run();

        $this->assertEquals(18, $result);

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
                ->andReturn('$__specification__assertCount += 2;')
                ->shouldReceive('getPreConditions')->times(4)
                    ->andReturn(array('$__specification__assertCount += 3'))->mock(); // and +4 for preconditions


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

        $this->assertEquals(30, $result);

    }
}
