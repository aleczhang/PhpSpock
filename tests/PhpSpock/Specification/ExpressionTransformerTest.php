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
/**
 * Date: 11/3/11
 * Time: 10:38 AM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock\Specification;

class ExpressionTransformerTest extends \PHPUnit_Framework_TestCase {

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }

    /**
     * @test
     */
    public function compileNonExpression()
    {
        $transformer = new ExpressionTransformer();
        $this->assertEquals('foo', $transformer->transform('foo'));
    }

    /**
     * @test
     * @dataProvider expressionExamples
     */
    public function compileMockExpressions($raw, $compiled)
    {
        $transformer = new ExpressionTransformer();
        $this->assertEquals($compiled, $transformer->transform($raw));
    }

    public function expressionExamples()
    {
        $simplePrefix = '$a->shouldReceive("getFoo")->withNoArgs()';
        $simpleSuffix  = '';


        $cardinalityExamples = array(
            array('1 * $a->getFoo()', $simplePrefix.'->once()'. $simpleSuffix ),
            array('2 * $a->getFoo()', $simplePrefix.'->twice()'. $simpleSuffix ),
            array('(1.._) * $a->getFoo()', $simplePrefix.'->atLeast()->times(1)'. $simpleSuffix ),
            array('(_..2) * $a->getFoo()', $simplePrefix.'->atMost()->times(2)'. $simpleSuffix ),
            array('(2..5) * $a->getFoo()', $simplePrefix.'->between(2, 5)'. $simpleSuffix ),
            array('0 * $a->getFoo()', $simplePrefix.'->never()'. $simpleSuffix ),
            array('(0.._) * $a->getFoo()', $simplePrefix.'->zeroOrMoreTimes()'. $simpleSuffix ),
            array('1 * $a->getFoo() >> true', $simplePrefix .'->once()->andReturn(true)'. $simpleSuffix ),

            // alternative syntax
            array('+1 * $a->getFoo()', $simplePrefix.'->atLeast()->times(1)'. $simpleSuffix ),
            array('-2 * $a->getFoo()', $simplePrefix.'->atMost()->times(2)'. $simpleSuffix ),
            array('2..5 * $a->getFoo()', $simplePrefix.'->between(2, 5)'. $simpleSuffix ),
            array('+0 * $a->getFoo()', $simplePrefix.'->zeroOrMoreTimes()'. $simpleSuffix ),
        );

        $argPrefix = '$a->shouldReceive("getFoo")';
        $argSuffix = '->once()';

        $argumentExamples = array(
            array('1 * $a->getFoo()', $argPrefix . '->withNoArgs()' . $argSuffix),
            array('1 * $a->getFoo(on(function(){ $a = array(1,2,3)}), 4, 5)', $argPrefix . '->with(\Mockery::on(function(){ $a = array(1,2,3)}), 4, 5)' . $argSuffix),
            array('1 * $a->getFoo(1)', $argPrefix . '->with(1)' . $argSuffix),
            array('1 * $a->getFoo(1,2,3,4,5,6,7,8)', $argPrefix . '->with(1, 2, 3, 4, 5, 6, 7, 8)' . $argSuffix),
            array('1 * $a->getFoo(_*_)', $argPrefix . '->withAnyArgs()' . $argSuffix),
            array('1 * $a->getFoo(1, 2, _)', $argPrefix . '->with(1, 2, \Mockery::any())' . $argSuffix),
            array('1 * $a->getFoo(_, _, _)', $argPrefix . '->with(\Mockery::any(), \Mockery::any(), \Mockery::any())' . $argSuffix),
            array('1 * $a->getFoo(!null)', $argPrefix . '->with(\Mockery::not(null))' . $argSuffix),
            array('1 * $a->getFoo(null)', $argPrefix . '->with(\Mockery::mustBe(null))' . $argSuffix),
            array('1 * $a->getFoo(\'/^foo/\')', $argPrefix . '->with(\'/^foo/\')' . $argSuffix),
            array('1 * $a->getFoo(mustBe($something))', $argPrefix . '->with(\Mockery::mustBe($something))' . $argSuffix),
            array('1 * $a->getFoo(mustBe(1))', $argPrefix . '->with(\Mockery::mustBe(1))' . $argSuffix),
            array('1 * $a->getFoo(not(1))', $argPrefix . '->with(\Mockery::not(1))' . $argSuffix),
            array('1 * $a->getFoo(anyOf(1,2,3))', $argPrefix . '->with(\Mockery::anyOf(1,2,3))' . $argSuffix),
            array('1 * $a->getFoo(notAnyof(1,2,3))', $argPrefix . '->with(\Mockery::notAnyof(1,2,3))' . $argSuffix),
            array('1 * $a->getFoo(subset(array(0=>\'foo\')))', $argPrefix . '->with(\Mockery::subset(array(0=>\'foo\')))' . $argSuffix),
            array('1 * $a->getFoo(contains("foo", $myVar))', $argPrefix . '->with(\Mockery::contains("foo", $myVar))' . $argSuffix),
            array('1 * $a->getFoo(hasKey("boo"))', $argPrefix . '->with(\Mockery::hasKey("boo"))' . $argSuffix),
            array('1 * $a->getFoo(hasValue("boo"))', $argPrefix . '->with(\Mockery::hasValue("boo"))' . $argSuffix),
        );

        $simplePrefix = '$a->shouldReceive("getFoo")->withNoArgs()->once()';
        $simpleSuffix  = '';

        return array_merge($cardinalityExamples, $argumentExamples);
    }
}
