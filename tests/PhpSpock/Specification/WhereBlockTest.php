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

use \PhpSpock\Specification\WhereBlock\Parameterization;

class WhereBlockTest extends \PHPUnit_Framework_TestCase {

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
        /**
         * @var $foo
         * @var $__parametrization__hasMoreVariants
         */

        $block = new WhereBlock();

        $p1 = new Parameterization();
        $p1->setLeftExpression('$foo');
        $p1->setRightExpression('array(1,2,3)');

        $block->setParametrizations(array($p1));

        $code = $block->compileCode(0);
        eval($code);
        $this->assertTrue($__parametrization__hasMoreVariants);
        $this->assertEquals(1, $foo);

        $code = $block->compileCode(1);
        eval($code);
        $this->assertTrue($__parametrization__hasMoreVariants);
        $this->assertEquals(2, $foo);

        $code = $block->compileCode(2);
        eval($code);

        $this->assertFalse($__parametrization__hasMoreVariants);
        $this->assertEquals(3, $foo);
    }

    /**
     * @test
     */
    public function compileCodeWithSetupBlock()
    {
        /**
         * @var $foo
         * @var $__parametrization__hasMoreVariants
         */

        $setup = new SimpleBlock();
        $setup->setCode('$boo = 123;');
        eval($setup->compileCode());

        $block = new WhereBlock();

        $p1 = new Parameterization();
        $p1->setLeftExpression('$foo');
        $p1->setRightExpression('array(1,2,$boo + 3)');

        $block->setParametrizations(array($p1));

        $code = $block->compileCode(0);
        eval($code);
        $this->assertTrue($__parametrization__hasMoreVariants);
        $this->assertEquals(1, $foo);

        $code = $block->compileCode(1);
        eval($code);
        $this->assertTrue($__parametrization__hasMoreVariants);
        $this->assertEquals(2, $foo);

        $code = $block->compileCode(2);
        eval($code);
        $this->assertFalse($__parametrization__hasMoreVariants);
        $this->assertEquals(126, $foo);
    }



    /**
     * @test
     */
    public function compileCodeWithSeveralParametrizations()
    {
        /**
         * @var $foo
         * @var $baz
         * @var $__parametrization__hasMoreVariants
         */

        $setup = new SimpleBlock();
        $setup->setCode('$boo = 123;');
        eval($setup->compileCode());

        $block = new WhereBlock();

        $p1 = new Parameterization();
        $p1->setLeftExpression('$foo');
        $p1->setRightExpression('array(1,2,$boo + 3)');

        $p2 = new Parameterization();
        $p2->setLeftExpression('$baz');
        $p2->setRightExpression('array($foo,$foo + 1)');

        $block->setParametrizations(array($p1, $p2));

        // step 1 : [11]
        $code = $block->compileCode(0);
        eval($code);
        $this->assertTrue($__parametrization__hasMoreVariants);
        $this->assertEquals(1, $foo);
        $this->assertEquals(1, $baz);

        // step 2 : [22]
        $code = $block->compileCode(1);
        eval($code);
        $this->assertTrue($__parametrization__hasMoreVariants);
        $this->assertEquals(2, $foo);
        $this->assertEquals(3, $baz);

        // step 3 : [31]
        $code = $block->compileCode(2);
        eval($code);
        $this->assertFalse($__parametrization__hasMoreVariants);
        $this->assertEquals(126, $foo);
        $this->assertEquals(126, $baz);

        // step 4 : [12]
        $code = $block->compileCode(3);
        eval($code);
        $this->assertFalse($__parametrization__hasMoreVariants);
        $this->assertEquals(1, $foo);
        $this->assertEquals(2, $baz);

        // step 5 : [21]
        $code = $block->compileCode(4);
        eval($code);
        $this->assertFalse($__parametrization__hasMoreVariants);
        $this->assertEquals(2, $foo);
        $this->assertEquals(2, $baz);
    }
}
