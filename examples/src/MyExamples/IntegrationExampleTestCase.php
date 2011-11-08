<?php

namespace MyExamples;

use \PhpSpock\Adapter\PhpUnitAdapter as PhpSpock;


class IntegrationExampleTestCase extends \PHPUnit_Framework_TestCase
{
    protected function runTest()
    {
        if (\PhpSpock\Adapter\PhpUnitAdapter::isSpecification($this)) {
            return \PhpSpock\Adapter\PhpUnitAdapter::runTest($this);
        } else {
            return parent::runTest();
        }
    }
}


