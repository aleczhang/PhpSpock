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

class SimpleBlockTest extends \PHPUnit_Framework_TestCase {

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
        $block = new SimpleBlock();
        $block->setCode('$foo = 1;');

        eval($block->compileCode());

        /**
         * @var $foo
         */
        $this->assertEquals(1, $foo);
    }


    /**
     * @test
     */
    public function compileCodeWithException()
    {
        $__specification_Exception = null;

        $block = new SimpleBlock();
        $block->setCode('throw new \RuntimeException("foo");');

        eval($block->compileCode());

        $this->assertInstanceOf('\RuntimeException', $__specification_Exception);
        $this->assertEquals('foo', $__specification_Exception->getMessage());
    }
}
