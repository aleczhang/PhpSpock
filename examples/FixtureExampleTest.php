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

class FixtureExampleTest extends IntegrationExampleTestCase
{
    /**
     * @var \Example\Calc
     */
    public $myResource;

    protected function setUp()
    {
        parent::setUp();

        $this->myResource = new Calc();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->myResource = null;
    }


    /**
     * @group debug
     * @spec
     */
    public function testIndex()
    {
        when:
        $b = $this->myResource->add(1, 3);

        then:
//        1 * $a->getCode() >> 1;
        $b == 4;
    }
}


