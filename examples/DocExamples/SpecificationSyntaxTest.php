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

namespace DocExamples;

class SpecificationSyntaxTest extends \MyExamples\IntegrationExampleTestCase
{
    /**
     * @spec
     * @test
     */
    public function makeSureIAmStillGoodInMathematics()
    {
        then:
        2 + 2 == 4;
    }

    /**
     * @spec
     * @test
     */
    public function makeSureIAmStillGoodInMathematicsWithExcept()
    {
        expect:
        2 + 2 == 4;
    }

    /**
     * @spec
     * @test
     *
     * remove this if you want to see test fail:
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     */
    public function assertionExamples()
    {
        expect:
        2 + 2 == 4;      // assertion - true, ignoring
        3 - 3;           // not an assertion - ignoring
        true;            // assertion - true, ignoring
        (bool) (2-2);    // assertion - expression is converted to boolean false, throwing an assertion exception
    }

    /**
     * @spec
     * @test
     */
    public function whenThenExample()
    {
        when:
        $a = 1 + 2;

        then:
        $a == 3;
    }

    /**
     * @spec
     * @test
     */
    public function whenThenExampleWithSeveralPairs()
    {
        when:
        $a = 1 + 2;

        then:
        $a == 3;

        when_:
        $a += 4;

        then_:
        $a == 7;
    }

    /**
     * @spec
     * @test
     */
    public function whenThenExampleWithMorePairs()
    {
        when:
        $a = 1 + 2;

        then:
        $a == 3;

        when_:
        $a += 4;

        then_:
        $a == 7;

        when__:
        $a -= 2;

        then__:
        $a == 5;
    }

    /**
     * @spec
     * @test
     */
    public function setupBlock()
    {
        setup:
        $a = 3 + rand(2, 4);

        expect:
        $a > 3;
    }

    /**
     * @spec
     * @test
     */
    public function setupBlockWithoutLabel()
    {
        $a = 3 + rand(2, 4);

        expect:
        $a > 3;
    }

    /**
     * @spec
     * @test
     */
    public function setupBlockWithCleanup()
    {
        setup:
        $temp = tmpfile();

        when:
        fwrite($temp, "writing to tempfile");

        then:
        notThrown('Exception');

        when_:
        fseek($temp, 0);
        $data = fread($temp, 1024);

        then_:
        $data == "writing to tempfile";

        cleanup:
        fclose($temp); // this removes the file according to tmpfile() docs
    }

    /**
     * @spec
     * @test
     */
    public function parametrizationArrayNotation()
    {
        /**
         * @var $a
         */

        expect:
        $a + 2 > 0;

        where:
        $a << array(1, 2, 3);
    }

    /**
     * @spec
     * @test
     */
    public function parametrizationTableNotation()
    {
        /**
         * @var $a
         * @var $b
         * @var $c
         */

        expect:
        $a + $b == $c;

        where:
        $a  | $b  | $c;
         1  |  2  |  3;
         3  |  2  |  5;
         3  |  4  |  7;
        -3  |  4  |  1;
    }

    /**
     * @spec
     * @test
     */
    public function parametrizationMixedNotation()
    {
        /**
         * @var $a
         * @var $b
         * @var $c
         * @var $d
         * @var $e
         * @var $f
         */

        expect:
        $a + $b + $c + $d + $e + $f > 0;

        where:
        $a  | $b  | $c;
         1  |  2  |  3;
         3  |  2  |  5;
         3  |  4  |  7;
        -3  |  4  |  1;

        $d << array(1, 2, 3);

        $e  | $f;
         2  |  3;
         2  |  5;
    }

    /**
     * @spec
     * @test
     */
    public function parametrizationValueRolling()
    {
        /**
         * @var $a
         * @var $b
         */

        expect:
        $a + $b > 0;

        where:
        $a << array(1, 2, 3);
        $b << array(1, 2);
    }

    /**
     * @spec
     * @test
     */
    public function parametrizationVariablesFromSetup()
    {
        /**
         * @var $a
         */
        setup:
        $b = 123;

        expect:
        $a + 1  > 100;

        where:
        $a << array($b + 1, $b + 3, 101);
    }

    /**
     * @spec
     * @test
     */
    public function parametrizationVariablesFromSetupInTable()
    {
        /**
         * @var $a
         * @var $c
         */
        setup:
        $b = 123;

        expect:
        $a + $c + 1  > 100;

        where:
        $a      | $c;
        $b + 1  | 1 ;
        2       | $b + 3;
        101     | 3;
    }

    /**
     * @spec
     * @test
     */
    public function parametrizationWithExternalValueSource()
    {
        /**
         * @var $word
         */
        setup:
        $myDataProvider = function() {
            return explode(' ', 'When in the Course of human events it becomes necessary for one people to dissolve the political bands which have connected them with another and to assume among the powers of the earth, the separate and equal station to which the Laws of Nature and of Nature\'s God entitle them, a decent respect to the opinions of mankind requires that they should declare the causes which impel them to the separation.');
        };

        expect:
        preg_match('/[a-zA-Z]{1,15}/', $word) == true;

        where:
        $word << $myDataProvider();
    }

}


