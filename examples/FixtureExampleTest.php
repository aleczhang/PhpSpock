<?php

namespace MyExamples;

use \Example\Calc;

class FixtureExampleTest extends IntegrationExampleTestCase
{
    /**
     * @var \Example\Calc
     */
    public $myResource;

    protected function setUp()
    {
        parent::setUp();

        $this->myResource = new Calc();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->myResource = null;
    }


    /**
     * @group debug
     * @spec
     * @specDebug
     */
    public function testIndex()
    {
        /**
         * @var $a \Example\Calc *Mock*
         * @var $ab \Example\Calc *Mock*
         * @var $a *Mock*
         */
        setup:

        when:
        $b = $this->myResource->add(1, 3);

        then:
//        1 * $a->getCode() >> 1;
        $b == 4;
    }

    function test__spec_debug_testIndex_Variant1() { 

        /**
         * Mocks
         */
         
        $_mock_a = \Mockery::mock('');
        $_mock_ab = \Mockery::mock('\Example\Calc');

                
        /**
         * When block
         */
        
        try {
            $b = $this->myResource->add(1, 3);
        } catch(\Exception $e) {
            $__specification_Exception = $e;
        }
        


        /**
         * Then block
         */
        
        $expressionResult = $b == 4;

        if (is_bool($expressionResult)) {

            if (!isset($__specification__assertCount)) {
                $__specification__assertCount = 0;
            }
            $__specification__assertCount++;

            if(!$expressionResult) {
                $msg = "Expression \$b == 4 is evaluated to false.";
                

                throw new \PhpSpock\Specification\AssertionException($msg);
            }
        }
        if (isset($__specification_Exception) && $__specification_Exception instanceof \Exception) {
            throw $__specification_Exception;
        }
        


    }
}


