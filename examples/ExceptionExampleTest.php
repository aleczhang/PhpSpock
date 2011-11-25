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

class ExceptionExampleTest extends IntegrationExampleTestCase
{

    /**
     * @spec
     * @expectedException RuntimeException
     */
    public function testIndex()
    {
        when:
        throw new \RuntimeException("test");

        then:
        $this->fail("Exception should be thrown!");
    }

    /**
     * @spec
     */
    public function testIndexWithNoExeptionThrown()
    {
        when:
        1==1;

        then:
        notThrown("RuntimeException");
    }

    /**
     * @spec
     */
    public function testIndexWithThrown()
    {
        when:
        throw new \Exception("test");

        then:
        thrown("Exception");
    }



    /**
     * @spec
     */
    public function testIndexWithThrown2()
    {
        when:
        throw new \RuntimeException("test");

        then:
        thrown("Exception");
    }

    /**
     * @spec
     */
    public function testIndexWithThrown3()
    {
        when:
        throw new \RuntimeException("test");

        then:
        thrown("RuntimeException");
    }

    /**
     * @spec
     */
    public function testIndexWithThrown4()
    {
        when:
        1==1;

        then:
        notThrown("RuntimeException");
    }

    /**
     * @spec
     */
    public function testExceptionCombination()
    {
        when:
        1==1;

        then:
        notThrown("RuntimeException");
        
        _when:
        throw new \RuntimeException("test");

        _then:
        thrown("RuntimeException");
    }
}


