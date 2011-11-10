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

class HelloSpockTest extends IntegrationExampleTestCase
{

    /**
     * @spec
     */
    public function testWithArrayNotation()
    {
        /**
         * @var $name
         * @var $size
         */

        expect:
        strlen($name) == $size;

        where:
        $name << array("Kirk", "Spock", "Scotty");
        $size << array(4, 5, 6);
    }

    /**
     * @spec
     */
    public function testWithTableNotation()
    {
        /**
         * @var $name
         * @var $size
         */

        expect:
        strlen($name) == $size;

        where:
        $name    | $size;
        "Kirk"   | 4;
        "Spock"  | 5;
        "Scotty" | 6;
    }
}


