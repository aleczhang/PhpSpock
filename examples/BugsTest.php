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

namespace MyExamples;

use \PhpSpock\Adapter\PhpUnitAdapter as PhpSpock;

class BugsTest extends \PHPUnit_Framework_TestCase
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
     * @specDebug
     */
    public function testIndex()
    {
        $a = 1;

        //;

        $a = 3;

        expect:
        $a == 3;
    }

    function test__spec_debug_testIndex_Variant1() { 

        /**
         * Setup block
         */
        $a = 1;
$a = 3;


        /**
         * Then block
         */
        
        if (isset($__specification_Exception) && $__specification_Exception instanceof \Exception) {
            throw $__specification_Exception;
        }
        $__specification__expressionResult = $a == 3;

        if (is_bool($__specification__expressionResult)) {

            if (!isset($__specification__assertCount)) {
                $__specification__assertCount = 0;
            }
            $__specification__assertCount++;

            if(!$__specification__expressionResult) {
                $__specification__msg = "Expression \$a == 3 is evaluated to false.";
                

                throw new \PhpSpock\Specification\AssertionException($__specification__msg);
            }
        }
        if (isset($__specification_Exception) && $__specification_Exception instanceof \Exception) {
            throw $__specification_Exception;
        }
        


    }
}


