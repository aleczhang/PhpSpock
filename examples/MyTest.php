<?php

namespace Example\Trololo;

use \PhpSpock\Adapter\PhpUnitAdapter as PhpSpock;

/**
 * @group myTestGroup
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
    protected function runTest()
    {
        if (\PhpSpock\Adapter\PhpUnitAdapter::isSpecification($this)) {
            return \PhpSpock\Adapter\PhpUnitAdapter::runTest($this);
        } else {
            return parent::runTest();
        }
    }


    public function testIndexSpec()
    {
        setup:
        $a = new PhpSpock();

        then:
        'PhpSpock\Adapter\PhpUnitAdapter' == get_class($a);

    }
}
