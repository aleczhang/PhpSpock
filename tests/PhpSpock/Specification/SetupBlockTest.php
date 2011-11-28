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

class SetupBlockTest extends \PHPUnit_Framework_TestCase {

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
        $block = new SetupBlock();
        $block->setExpressions(
            array(
                '$foo = 1',
                '$foo == 1'
            )
        );

        $this->assertEquals('$foo = 1;'."\n".'$foo == 1;', $block->compileCode());
    }

    /**
     * @test
     */
    public function compileCodeShouldCallExpressionTransformer()
    {
        $transformer = \Mockery::mock('PhpSpock\Specification\ExpressionTransformer');
        $transformer->shouldReceive('transform')->with('$foo = 1')->andReturn('baz');

        $block = new SetupBlock();
        $block->setExpressionTransformer($transformer);
        $block->setExpressions(
            array(
                '$foo = 1'
            )
        );

        $this->assertEquals('baz;', $block->compileCode());
    }
}
