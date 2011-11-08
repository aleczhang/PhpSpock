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


    /**
     * @return void
     * @spec
     */
    public function testIndex()
    {
        /**
         * @var $b
         */

        setup:
        $a = new PhpSpock();

        when:
        2 + 2;

        then:
        $this->assertType('PhpSpock\Adapter\PhpUnitAdapter', $a);
        'PhpSpock\Adapter\PhpUnitAdapter' == get_class($a);

        where:
        $b << array(1, 2, 3);
    }
}


