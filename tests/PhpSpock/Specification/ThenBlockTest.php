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

class ThenBlockTest extends \PHPUnit_Framework_TestCase {

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }

    /**
     * @test
     */
    public function compileCode()
    {
        $foo = 0;
        
        $block = new ThenBlock();

        $expr1 = new \PhpSpock\Specification\ThenBlock\Expression();
        $expr1->setCode('$foo = 1');

        $block->setExpressions(array($expr1));

        $code = $block->compileCode();

        $this->assertNotNull($code);

        eval($code);
        $this->assertEquals(1, $foo);
    }


    /**
     * @test
     */
    public function compileCodeWithExpression()
    {
        $foo = 0;

        $block = new ThenBlock();

        $expr1 = new \PhpSpock\Specification\ThenBlock\Expression();
        $expr1->setCode('1 * $a->get()');

        $expr2 = new \PhpSpock\Specification\ThenBlock\Expression();
        $expr2->setCode('$foo = 1');

        $block->setExpressions(array($expr1, $expr2));

        $code = $block->compileCode();

        $conditions = $block->getPreConditions();
        $this->assertEquals(1, count($conditions));
        $this->assertEquals('$a->shouldReceive("get")->withNoArgs()->once()', $conditions[0]);

        $this->assertNotNull($code);

        eval($code);
        $this->assertEquals(1, $foo);
    }



    /**
     * @test
     */
    public function bugWithQuotationInjection()
    {
        $foo = 0;

        $expr1 = new \PhpSpock\Specification\ThenBlock\Expression();
        $expr1->setCode('$foo = "123"');

        $block = new ThenBlock();
        $block->setExpressions(
            array(
               $expr1
            )
        );

        $code = $block->compileCode();

        $this->assertNotNull($code);

        eval($code);
    }

    /**
     * @test
     */
    public function testCodeWithBooleanExpressionThatIsTrue()
    {
        $foo = 0;

        $expr1 = new \PhpSpock\Specification\ThenBlock\Expression();
        $expr1->setCode('$foo = 1');

        $expr2 = new \PhpSpock\Specification\ThenBlock\Expression();
        $expr2->setCode('$foo == 1');

        $block = new ThenBlock();
        $block->setExpressions(
            array(
               $expr1,
               $expr2
            )
        );

        $code = $block->compileCode();

        $this->assertNotNull($code);

        eval($code);
        $this->assertEquals(1, $foo);
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function exceptionInWhenBlock()
    {
        $foo = 0;
        $__specification_Exception = new \RuntimeException('test');

        $expr1 = new \PhpSpock\Specification\ThenBlock\Expression();
        $expr1->setCode('$foo = 1');

        $block = new ThenBlock();
        $block->setExpressions(
            array(
               $expr1
            )
        );

        $code = $block->compileCode();

        $this->assertNotNull($code);

        eval($code);
    }

//    /**
//     * @test
//     */
//    public function exceptionInWhenBlockWithThrown()
//    {
//        $foo = 0;
//        $__specification_Exception = new \RuntimeException('test');
//
//        $expr1 = new \PhpSpock\Specification\ThenBlock\Expression();
//        $expr1->setCode('thrown("RuntimeException")');
//
//        $expr2 = new \PhpSpock\Specification\ThenBlock\Expression();
//        $expr2->setCode('$foo = 3');
//
//        $block = new ThenBlock();
//        $block->setExpressions(
//            array(
//               $expr1
//            )
//        );
//
//        $code = $block->compileCode();
//
//        $this->assertNotNull($code);
//
//        eval($code);
//
//        $this->assertEquals(3, $foo);
//    }

    /**
     * @test
     */
    public function testCodeWithBooleanExpressionThatIsFalse()
    {
        $foo = 0;


        $expr1 = new \PhpSpock\Specification\ThenBlock\Expression();
        $expr1->setCode('$foo = 1');

        $expr2 = new \PhpSpock\Specification\ThenBlock\Expression();
        $expr2->setCode('$foo == 2');
        $expr2->setComment('booo!');

        $block = new ThenBlock();
        $block->setExpressions(
            array(
                $expr1,
                $expr2
            )
        );

        $code = $block->compileCode();

        $this->assertNotNull($code);

        try {
            eval($code);
            $this->fail("Assert exception expected");
        } catch(\PhpSpock\Specification\AssertionException $e) {

            $this->assertContains('booo!', $e->getMessage());
        }
    }
}
