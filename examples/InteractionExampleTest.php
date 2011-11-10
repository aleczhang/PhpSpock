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

use \Example\Calc;

class InteractionExampleTest extends IntegrationExampleTestCase
{
    /**
     * @spec
     */
    public function test1()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */
        setup:
        1 * $a->add(_,_);

        when:
        $a->add(1,2);

        then:
        notThrown();
    }


    /**
     * @spec
     */
    public function test2()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */

        when:
        $a->add(1,2);

        then:
        1 * $a->add(_,_);

        when_:
        $a->add(1,2);

        then_:
        1 * $a->add(_,_);

        when__:
        1==1;

        then__:
        1 * $a->add(_,_);
    }


    /**
     * @spec
     */
    public function test3()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */

        when:
        $b = $a->add(1,2);

        then:
        1 * $a->add(_,_) >> 4;
        $b == 4;
    }


    /**
     * @spec
     */
    public function test5()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */

        when:
        $b = $a->add(1,2);

        then:
        1 * $a->add(_,_) >> usingClosure(function($one, $two) { return $one + $two + 1; });
        $b == 4;
    }


    /**
     * @spec
     * @expectedException \RuntimeException
     * @expectedExceptionMessage foo
     */
    public function test6()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */

        when:
        $b = $a->add(1,2);

        then:
        1 * $a->add(_,_) >> throws(new \RuntimeException('foo'));
        $b == 4;
    }

    /**
     * @spec
     * @expectedException \RuntimeException
     * @expectedExceptionMessage foo
     */
    public function test7()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */

        when:
        $b = $a->add(1,2);

        then:
        1 * $a->add(_,_) >> throws('RuntimeException', 'foo');
        $b == 4;
    }


    /**
     * @spec
     */
    public function test8()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */
        setup:
        1 * $a->add(_,_) >> throws(new \RuntimeException('foo'));

        when:
        $b = $a->add(1,2);

        then:
        thrown();
    }

    /**
     * @spec
     */
    public function test9()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */
        setup:
        1 * $a->add(_,_) >> throws('RuntimeException', 'foo');

        when:
        $b = $a->add(1,2);

        then:
        thrown('RuntimeException');
    }

    /**
     * @spec
     */
    public function testSetupBlockInteractions()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */
        setup:
        (0.._) * $a->add(1, 2) >> 3;
        (0.._) * $a->add(2, 2) >> 4;

        when:
        $b = $a->add(1,2);

        then:
        $b == 3;
    }



    /**
     * @spec
     */
    public function testThenBlockInteractions()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */
        when:
        $b = $a->add(1,2);

        then:
        1 * $a->add(1,2) >> 3;
        $b == 3;
    }

}


