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

class PhpSpecTest extends \PHPUnit_Framework_TestCase
{

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
        $phpSpock = new PhpSpock();

        $phpSpock->run(function()
            {
                setup:
                $a = 1;

                when:
                $a++;

                then:
                $a == 2;
            });
    }



    /**
     * @test
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     */
    public function executeSpecificationWithPhpUnitAdapter()
    {
        $phpSpock = new PhpSpock();

        $phpSpock->runWithAdapter(
            new \PhpSpock\Adapter\PhpUnitAdapter(),
            function()
            {
                then:
                2 + 2 == 5;
            });
    }


    /**
     * @test
     */
    public function executeSpecificationWithParametrizationInTableStyle()
    {
        $phpSpock = new PhpSpock();

        $phpSpock->runWithPhpUnit(function()
            {
                /**
                 * @var $left
                 * @var $right
                 * @var $result
                 * @var $expectedResult
                 */

                setup:
                $a = 1;

                when:
                $a++;
                $result = $right * $left;

                then:
                $a == 2;
                $result == $expectedResult;

                where:
                $left | $right | $expectedResult;
                2 | 3 | 6;
                3 | 5 | 15;
                7 | 8 | 55;
            });
    }

    /**
     * @test
     */
    public function executeSpecificationWithParametrizationInArrayStyle()
    {
        $phpSpock = new PhpSpock();

        $phpSpock->run(function()
            {
                /**
                 * @var $b
                 * @var $result
                 */

                setup:
                $a = 1;

                when:
                $result = 5 + $b -5;

                then:
                $result == $b;

                where:
                $b << array($a, 1, 2);
            });
    }

    /**
     * @test
     * @expectedException PhpSpock\Specification\AssertionException
     */
    public function executeSpecificationWithBrokenTest()
    {
        $phpSpock = new PhpSpock();

        $phpSpock->run(function()
            {
                setup:
                $a = 1;

                when:
                $a++;

                then:
                $a == 3;
            });
    }

    /**
     * @test
     * @expectedException PhpSpock\Specification\AssertionException
     */
    public function executeTestFromCallback()
    {
        $phpSpock = new PhpSpock();
        $phpSpock->run(array($this, 'mySpec'));
    }

    public function mySpec()
    {
        setup:
        $a = 1;

        when:
        $a++;

        then:
        $a == 3;
    }
}
