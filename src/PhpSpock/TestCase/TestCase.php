<?php
/**
 * Date: 11/15/11
 * Time: 1:34 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock\TestCase;

class TestCase extends \PHPUnit_Framework_TestCase {

    protected function runTest()
    {
        if (\PhpSpock\Adapter\PhpUnitAdapter::isSpecification($this)) {
            return \PhpSpock\Adapter\PhpUnitAdapter::runTest($this);
        } else {
            return parent::runTest();
        }
    }
}
