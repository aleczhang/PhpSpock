<?php

namespace MyExamples;

use \PhpSpock\Adapter\PhpUnitAdapter as PhpSpock;

class WithoutIntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected function runTest()
    {
        if (\PhpSpock\Adapter\PhpUnitAdapter::isSpecification($this)) {
            return \PhpSpock\Adapter\PhpUnitAdapter::runTest($this);
        } else {
            return parent::runTest();
        }
    }


    /**
     * @return void
     * @spec
     */
    public function testIndex()
    {
        setup:
        $calc = new \Example\Calc();

        when:
        $b = $calc->add(1, 3);

        then:
        $b == 4;
    }
}


