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

namespace PhpSpock\Specification\ThenBlock;

class ExpressionTest extends \PHPUnit_Framework_TestCase {

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }

//    /**
//     * @test
//     */
//    public function splitCodeTest()
//    {
//        $exp = new Expression();
//        $exp->setCode('$foo->bar->baz()');
//
//        $parts = $exp->splitCode();
//
//        $this->assertEquals(
//            array(
//                'parts' => array(
//                    'exp1' => array(
//                        '$foo',
//                        'bar',
//                        'baz()'
//                    )
//                ),
//                'pattern' => '$exp1'
//            ),
//            $parts);
//    }


    /**
     * @test
     */
    public function expressionShouldNotCompileCommentOnlyExpression()
    {
        $__specification__expressionResult = null;

        $exp = new Expression();
        $exp->setCode('// hoho');

        $code = $exp->compile();

        eval($code);

        $this->assertNull($__specification__expressionResult);
    }

    /**
     * @test
     */
    public function expressionShouldReturnExpressionResultVariable()
    {
        $__specification__expressionResult = null;

        $exp = new Expression();
        $exp->setCode('123');

        $code = $exp->compile();

        eval($code);

        /**
         * @var $foo
         */
        $this->assertEquals(123, $__specification__expressionResult);
    }

    /**
     * @test
     */
    public function thrownShouldCheckWhetherExceptionWasThrown()
    {
        $exp = new Expression();
        $exp->setCode('thrown("RuntimeException")');

        $code = $exp->compile();

        $__specification_Exception = null;

        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertFalse($__specification__expressionResult);
    }

    /**
     * @test
     */
    public function thrownShouldBeConvertedIntoSomethingUseful()
    {
        $exp = new Expression();
        $exp->setCode('thrown()');

        $code = $exp->compile();

        $__specification_Exception = null;

        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertFalse($__specification__expressionResult);

        $__specification_Exception = new \Exception('foo');

        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertTrue($__specification__expressionResult);
        $this->assertNull($__specification_Exception);
    }

    /**
     * @test
     */
    public function thrownShouldBeConvertedIntoSomethingUseful2()
    {
        $exp = new Expression();
        $exp->setCode('notThrown()');

        $code = $exp->compile();

        $__specification_Exception = null;

        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertTrue($__specification__expressionResult);

        $__specification_Exception = new \Exception('foo');

        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertFalse($__specification__expressionResult);
        $this->assertNull($__specification_Exception);
    }

    /**
     * @test
     */
    public function thrownShouldBeConvertedIntoSomethingUseful_WithExceptionType()
    {
        $exp = new Expression();
        $exp->setCode('thrown("RuntimeException")');

        $code = $exp->compile();

        $__specification_Exception = null;

        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertFalse($__specification__expressionResult);

        $__specification_Exception = new \Exception('foo');

        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertFalse($__specification__expressionResult);
        $this->assertNull($__specification_Exception);
    }

    /**
     * @test
     */
    public function thrownShouldBeConvertedIntoSomethingUseful_WithExceptionType2()
    {
        $exp = new Expression();
        $exp->setCode('thrown("RuntimeException")');

        $code = $exp->compile();

        $__specification_Exception = null;

        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertFalse($__specification__expressionResult);

        $__specification_Exception = new \RuntimeException('foo');

        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertTrue($__specification__expressionResult);
        $this->assertNull($__specification_Exception);
    }

    /**
     * @test
     */
    public function thrownShouldBeConvertedIntoSomethingUseful_WithExceptionType3()
    {
        $exp = new Expression();
        $exp->setCode('notThrown("RuntimeException")');

        $code = $exp->compile();

        $__specification_Exception = null;

        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertTrue($__specification__expressionResult);

        $__specification_Exception = new \RuntimeException('foo');

        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertFalse($__specification__expressionResult);
        $this->assertNotNull($__specification_Exception);
    }

    /**
     * @test
     */
    public function thrownShouldBeConvertedIntoSomethingUseful_WithExceptionType4()
    {
        $exp = new Expression();
        $exp->setCode('notThrown("RuntimeException")');

        $code = $exp->compile();

        $__specification_Exception = null;

        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertTrue($__specification__expressionResult);

        $__specification_Exception = new \Exception('foo');


        /**
         * @var $__specification__expressionResult;
         */
        eval($code);
        $this->assertTrue($__specification__expressionResult);
        $this->assertNotNull($__specification_Exception);
    }

}
